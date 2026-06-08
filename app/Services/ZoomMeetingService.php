<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;

class ZoomMeetingService
{
    protected $client;
    protected $accessToken;
    protected $tokenExpiresAt;

    public function __construct()
    {
        $this->client = new Client(['verify' => false, 'timeout' => 30]);
        $this->ensureAccessToken();
    }

    /**
     * Ensure we have a valid access token
     */
    protected function ensureAccessToken()
    {
        if (!$this->accessToken || $this->tokenExpiresAt <= now()) {
            $this->refreshAccessToken();
        }
    }

    /**
     * Refresh the access token
     */
    protected function refreshAccessToken()
    {
        try {
            $clientId = config('zoom.client_id');
            $clientSecret = config('zoom.client_secret');
            $accountId = config('zoom.account_id');

            $response = $this->client->post('https://zoom.us/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                ],
                'form_params' => [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $this->accessToken = $data['access_token'];
            $this->tokenExpiresAt = now()->addSeconds($data['expires_in'] - 300);
        } catch (\Exception $e) {
            Log::error("Failed to refresh Zoom access token: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get current access token
     */
    protected function getAccessToken()
    {
        $this->ensureAccessToken();
        return $this->accessToken;
    }

    /**
     * Create a single meeting with registration enabled
     */
    public function createMeeting(Session $session)
    {
        try {
            $token = $this->getAccessToken();

            // Get raw value without HtmlString casting
            $rawAgenda = $session->getRawOriginal('session_objectives');

            // Clean the agenda text
            $agenda = $rawAgenda ?? 'Session meeting';
            if (is_string($agenda)) {
                // Strip HTML tags if you want plain text for Zoom
                $agenda = strip_tags($agenda);
                $agenda = html_entity_decode($agenda, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $agenda = preg_replace('/\s+/', ' ', trim($agenda));
            }

            // If agenda is empty after cleaning, use session name
            if (empty($agenda)) {
                $agenda = $session->session_name ?? 'Session meeting';
            }

            // Limit length
            $agenda = \Str::limit($agenda, 1990);

            $response = $this->client->post("https://api.zoom.us/v2/users/me/meetings", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'topic' => $session->session_name,
                    'agenda' => $agenda,
                    'type' => 2, // Scheduled meeting
                    'duration' => (int)$session->session_duration ?? 60,
                    'timezone' => 'America/Toronto',
                    'start_time' => $this->formatStartTime($session),
                    'settings' => [
                        'approval_type' => 0, // no registration 
                        'registration_type' => 1, // Require registration to join
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                        'waiting_room' => false,
                        'host_video' => true,
                        'participant_video' => false,
                        'registrants_email_notification' => false,
                    ],
                ],
            ]);

            $meeting = json_decode($response->getBody(), true);

            return [
                'id' => $meeting['id'],
                'join_url' => $meeting['join_url'],
                'password' => $meeting['password'] ?? null,
                'start_url' => $meeting['start_url'],
                'registration_url' => $meeting['registration_url'] ?? null
            ];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $error = $response['message'] ?? $e->getMessage();
            throw new \Exception("Zoom API error: " . $error);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create meeting: " . $e->getMessage());
        }
    }

    /**
     * Add a registrant to an existing meeting and get unique join URL
     */
    public function addRegistrant($meetingId, User $user)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->client->post("https://api.zoom.us/v2/meetings/{$meetingId}/registrants", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $user->email,
                    'first_name' => explode(' ', trim($user->name))[0] ?? $user->name,
                    'last_name' => explode(' ', trim($user->name))[1] ?? $user->name,
                    'auto_approve' => true,
                ],
            ]);

            $registrant = json_decode($response->getBody(), true);

            Log::info("User {$user->email} registered successfully with ID: {$registrant['registrant_id']}");

            return [
                'id' => $registrant['registrant_id'],
                'join_url' => $registrant['join_url']
            ];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);

            // If already registered, try to get existing registration
            if (strpos($response['message'] ?? '', 'already registered') !== false) {
                Log::warning("User {$user->email} already registered for meeting {$meetingId}");
                return $this->getExistingRegistrant($meetingId, $user->email);
            }

            $error = $response['message'] ?? $e->getMessage();
            throw new \Exception("Registration error for {$user->email}: " . $error);
        } catch (\Exception $e) {
            throw new \Exception("Failed to register user {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Get existing registrant details if already registered
     */
    protected function getExistingRegistrant($meetingId, $email)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->client->get("https://api.zoom.us/v2/meetings/{$meetingId}/registrants", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $registrants = json_decode($response->getBody(), true);

            foreach ($registrants['registrants'] as $registrant) {
                if ($registrant['email'] === $email) {
                    return [
                        'id' => $registrant['id'],
                        'join_url' => $registrant['join_url']
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("Failed to get existing registrant: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a host key for an instructor (to start meeting)
     * Note: Multiple instructors can use the same start_url, but each can have their own host key
     */
    public function generateHostKey($meetingId, User $instructor)
    {
        try {
            $token = $this->getAccessToken();

            // Generate a unique host key for this instructor
            // Zoom doesn't provide an API to generate host keys, but we can:
            // Option 1: Use the same start_url for all instructors (simpler)
            // Option 2: Create alternative hosts (requires paid Zoom plan)

            // For simplicity, we'll use the start_url from the meeting
            // All instructors can use the same start_url to host the meeting
            // Zoom will ask for the host key (which is set in meeting settings)

            $response = $this->client->get("https://api.zoom.us/v2/meetings/{$meetingId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $meeting = json_decode($response->getBody(), true);

            return [
                'start_url' => $meeting['start_url'],
                'host_key' => $meeting['host_key'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get meeting details for instructor: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add an alternative host (if using paid plan with alternative hosts feature)
     * This requires the instructor to have a Zoom account and be added as alternative host
     */
    public function addAlternativeHost($meetingId, User $instructor)
    {
        try {
            $token = $this->getAccessToken();

            // Get the instructor's Zoom email (they need a Zoom account)
            $instructorZoomEmail = $instructor->email; // Assuming they use same email

            $response = $this->client->patch("https://api.zoom.us/v2/meetings/{$meetingId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'settings' => [
                        'alternative_hosts' => $instructorZoomEmail,
                        'alternative_hosts_email_notification' => true
                    ]
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to add alternative host: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing meeting
     */
    public function updateMeeting(Session $session)
    {
        if (!$session->zoom_meeting_id) {
            return false;
        }

        try {
            $token = $this->getAccessToken();

            // Get raw value without HtmlString casting
            $rawAgenda = $session->getRawOriginal('session_objectives');

            // Clean the agenda text
            $agenda = $rawAgenda ?? 'Session meeting';
            if (is_string($agenda)) {
                // Strip HTML tags if you want plain text for Zoom
                $agenda = strip_tags($agenda);
                $agenda = html_entity_decode($agenda, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $agenda = preg_replace('/\s+/', ' ', trim($agenda));
            }

            // If agenda is empty after cleaning, use session name
            if (empty($agenda)) {
                $agenda = $session->session_name ?? 'Session meeting';
            }

            // Limit length
            $agenda = \Str::limit($agenda, 1990);

            $this->client->patch("https://api.zoom.us/v2/meetings/{$session->zoom_meeting_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'topic' => $session->session_name,
                    'agenda' => $agenda,
                    'duration' => $session->session_duration ?? 60,
                    'start_time' => $this->formatStartTime($session),
                    'settings' => [
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                    ],
                ],
            ]);

            Log::info("Meeting {$session->zoom_meeting_id} updated successfully");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update Zoom meeting: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel a registrant's registration - FIXED VERSION
     */
    public function cancelRegistration($meetingId, $registrantId)
    {
        // Validate inputs
        if (!$meetingId) {
            Log::warning("Cannot cancel registration: Missing meeting ID");
            return false;
        }

        if (!$registrantId) {
            Log::warning("Cannot cancel registration: Missing registrant ID");
            return false;
        }

        // Make sure registrant ID is not the same as meeting ID
        if ($registrantId == $meetingId) {
            Log::warning("Invalid registrant ID: Registrant ID cannot be the same as meeting ID");
            return false;
        }

        try {
            $token = $this->getAccessToken();

            // Correct URL format: meetings/{meetingId}/registrants/{registrantId}
            $url = "https://api.zoom.us/v2/meetings/{$meetingId}/registrants/{$registrantId}";
            Log::info("Attempting to cancel registration: {$url}");

            $response = $this->client->delete($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            Log::info("Registration cancelled successfully for registrant {$registrantId}");
            return true;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $response = json_decode($e->getResponse()->getBody(), true);
            $error = $response['message'] ?? $e->getMessage();

            // If registrant not found, it might already be cancelled
            if ($statusCode == 400 && strpos($error, 'not found') !== false) {
                Log::warning("Registrant {$registrantId} already cancelled or not found");
                return true; // Consider it as success since the end state is achieved
            }

            Log::error("Failed to cancel registration for {$registrantId}: " . $error);
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to cancel registration for {$registrantId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete the entire meeting
     */
    public function deleteMeeting($meetingId)
    {
        if (!$meetingId) {
            return false;
        }

        try {
            $token = $this->getAccessToken();

            $this->client->delete("https://api.zoom.us/v2/meetings/{$meetingId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            Log::info("Zoom meeting {$meetingId} deleted successfully");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete Zoom meeting: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format start time for Zoom API
     */
    private function formatStartTime($session)
    {
        $mapping = $session->schedules->first();

        if (!$mapping) {
            $today = Carbon::now()->addDay()->format('Y-m-d H:i:s');
            $startTime = Carbon::parse($today, 'America/Toronto');

            // Zoom accepts ISO 8601 format WITH timezone offset
            // This will output: 2025-01-15T14:30:00-05:00 (or -04:00 for daylight savings)
            return $startTime->toIso8601String();
        }

        // return Carbon::parse($mapping->schedule_date . ' ' . $mapping->schedule_time)
        //     ->format('Y-m-d H:i:s');

        // Parse the date and time in Toronto timezone
        $startTime = Carbon::parse($mapping->schedule_date . ' ' . $mapping->schedule_time, 'America/Toronto');

        // Zoom accepts ISO 8601 format WITH timezone offset
        // This will output: 2025-01-15T14:30:00-05:00 (or -04:00 for daylight savings)
        return $startTime->toIso8601String();
    }

    /**
     * Get the latest meeting details, including a fresh start_url for the host.
     */
    public function getMeetingForHost($meetingId)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->client->get("https://api.zoom.us/v2/meetings/{$meetingId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $meeting = json_decode($response->getBody(), true);

            // This will contain a new, valid start_url.
            return [
                'start_url' => $meeting['start_url'],
            ];
        } catch (\Exception $e) {
            Log::error("Failed to fetch meeting for host: " . $e->getMessage());
            return null;
        }
    }
}
