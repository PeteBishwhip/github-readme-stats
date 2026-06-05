<?php

return [
    'github' => [
        'base_url' => env('GITHUB_API_BASE_URL', 'https://api.github.com'),
        'token' => env('GITHUB_TOKEN'),
    ],

    'wakatime' => [
        'base_url' => env('WAKATIME_API_BASE_URL', 'https://wakatime.com/api/v1'),
        'api_key' => env('WAKATIME_API_KEY'),
    ],

    'http' => [
        'connect_timeout' => (int) env('READMESTATS_CONNECT_TIMEOUT', 3),
        'timeout' => (int) env('READMESTATS_TIMEOUT', 10),
    ],
];
