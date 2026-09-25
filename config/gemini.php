<?php

return [
    'api_key' => env('GEMINI_API_KEY', 'dummy-gemini-api-key'),
    'model' => env('GEMINI_MODEL', 'gemini-3.5-flash'),
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    'timeout_seconds' => (int) env('GEMINI_TIMEOUT', 15),
];
