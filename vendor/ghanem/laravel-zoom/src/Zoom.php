<?php

namespace Ghanem\Zoom;

use GuzzleHttp\Client;

class Zoom
{
    protected string $accessToken;
    protected $client;
    protected $account_id;
    protected $client_id;
    protected $client_secret;

    public function __construct()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->client_id = method_exists($user, 'clientID') ? $user->clientID() : config('zoom.client_id');
            $this->client_secret = method_exists($user, 'clientSecret') ? $user->clientSecret() : config('zoom.client_secret');
            $this->account_id = method_exists($user, 'accountID') ? $user->accountID() : config('zoom.account_id');
        } else {
            $this->client_id = config('zoom.client_id');
            $this->client_secret = config('zoom.client_secret');
            $this->account_id = config('zoom.account_id');
        }

        $this->accessToken = $this->getAccessToken();

        $this->client = new Client([
            'base_uri' => 'https://api.zoom.us/v2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function getAccessToken()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                'Host' => 'zoom.us',
            ],
        ]);

        $response = $client->request('POST', "https://zoom.us/oauth/token", [
            'form_params' => [
                'grant_type' => 'account_credentials',
                'account_id' => $this->account_id,
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);
        return $responseBody['access_token'];
    }

    // create meeting
    public function createMeeting(array $data, string $userId = 'me')
    {
        try {
            $response = $this->client->request('POST', 'users/' . $userId . '/meetings', [
                'json' => $data,
            ]);
            $res = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $res,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get meeting
    public function getMeeting(string $meetingId)
    {
        try {
            $response = $this->client->request('GET', 'meetings/' . $meetingId);
            $data = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get all meetings
    public function getAllMeeting()
    {
        try {
            $response = $this->client->request('GET', 'users/me/meetings');
            $data = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get upcoming meetings
    public function getUpcomingMeeting()
    {
        try {
            $response = $this->client->request('GET', 'users/me/meetings?type=upcoming');

            $data = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get previous meetings
    public function getPreviousMeetings()
    {
        try {
            $meetings = $this->getAllMeeting();

            $previousMeetings = [];

            foreach ($meetings['meetings'] as $meeting) {
                $start_time = strtotime($meeting['start_time']);

                if ($start_time < time()) {
                    $previousMeetings[] = $meeting;
                }
            }

            return [
                'status' => true,
                'data' => $previousMeetings
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get rescheduling meeting
    public function rescheduleMeeting(string $meetingId, array $data)
    {
        try {
            $response = $this->client->request('PATCH', 'meetings/' . $meetingId, [
                'json' => $data,
            ]);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Rescheduled Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // end meeting
    public function endMeeting($meetingId)
    {
        try {
            $response = $this->client->request('PUT', 'meetings/' . $meetingId . '/status', [
                'json' => [
                    'action' => 'end',
                ],
            ]);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Ended Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // delete meeting
    public function deleteMeeting(string $meetingId)
    {
        try {
            $response = $this->client->request('DELETE', 'meetings/' . $meetingId);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Deleted Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // recover meeting
    public function recoverMeeting($meetingId)
    {
        try {
            $response = $this->client->request('PUT', 'meetings/' . $meetingId . '/status', [
                'json' => [
                    'action' => 'recover',
                ],
            ]);

            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Recovered Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // get users list
    public function getUsers($data = null)
    {
        try {
            $response = $this->client->request('GET', 'users', [
                'query' => [
                    'page_size' => @$data['page_size'] ?? 300,
                    'status' => @$data['status'] ?? 'active',
                    'page_number' => @$data['page_number'] ?? 1,
                ],
            ]);
            $responseData = json_decode($response->getBody(), true);
            $data = [];
            $data['current_page'] = $responseData['page_number'];
            $data['users'] = $responseData['users'];
            $data['last_page'] = $responseData['page_count'];
            $data['per_page'] = $responseData['page_size'];
            $data['total'] = $responseData['total_records'];
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }


    // check User Email
    public function checkUserEmail($email)
    {
        try {
            $response = $this->client->request('GET', 'users/email?email=' . $email);

            $data = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }

    // create Users
    public function createUsers(array $data, string $userId = 'me')
    {
        try {
            $response = $this->client->request('POST', 'users', [
                'json' => $data,
            ]);
            $res = json_decode($response->getBody(), true);
            return [
                'status' => true,
                'data' => $res,
            ];
        } catch (\Throwable $th) {
            return $this->handelMessage($th);
        }
    }


    protected function handelMessage($th)
    {
        $message = $th->getMessage();
        if (preg_match('~\{(?:[^{}]|(?R))*\}~', $th->getMessage(), $string_response)) {
            $json_error_response = json_decode($string_response[0], true);
            if (isset($json_error_response['message'])) {
                $message = $json_error_response['message'];
            }
        }

        return [
            'status' => false,
            'message' => $message,
        ];
    }
}
