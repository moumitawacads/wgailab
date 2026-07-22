<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TestimonialsService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = env('WG_BASE_URL');
    }

    public function getTestimonials()
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/api/v1/testimonials");

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error get testimonials: ' . $e->getMessage());
            return ['error' => 'Failed to get testimonials'];
        }
    }
}
