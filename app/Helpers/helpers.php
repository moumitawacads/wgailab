<?php

use App\Models\Settings;

if (!function_exists('admin_settings')) {
    function admin_settings($key, $default = null)
    {
        static $settings = null;

        // Load once per request (performance optimized)
        if ($settings === null) {
            $settings = Settings::pluck('option_value', 'option_name')->toArray();
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('callApi')) {
    function callApi($method, $url, $data = [], $headers = [])
    {
        $ch = curl_init();

        $defaultHeaders = [
            'Authorization: Bearer ' . env('GHL_API_KEY'),
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers)
        ];

        $method = strtoupper($method);

        // Keep existing POST behavior exactly as-is
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        // Add PUT support only
        elseif ($method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        // Optional PATCH support
        elseif ($method === 'PATCH') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        // Optional DELETE support
        elseif ($method === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}