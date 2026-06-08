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