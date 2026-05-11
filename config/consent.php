<?php

return [
    'mode' => env('CONSENT_MODE', 'strict'),
    'driver' => env('ANALYTICS_DRIVER', 'none'),
    'cookie_name' => env('CONSENT_COOKIE_NAME', 'sidewalk_consent'),
    'categories' => [
        [
            'key' => 'necessary',
            'label' => 'Necessary',
            'description' => 'Required for core delivery, navigation, and the consent state itself.',
            'readonly' => true,
            'enabled' => true,
        ],
        [
            'key' => 'analytics',
            'label' => 'Analytics',
            'description' => 'Cookie-less, opt-in measurement. Disabled until you accept this category.',
            'readonly' => false,
            'enabled' => false,
        ],
        [
            'key' => 'media',
            'label' => 'Media',
            'description' => 'Controls third-party embeds such as YouTube, maps, and other iframe-based services.',
            'readonly' => false,
            'enabled' => false,
        ],
    ],
    'services' => [
        'analytics' => [
            'driver' => env('ANALYTICS_DRIVER', 'none'),
            'umami' => [
                'website_id' => env('UMAMI_WEBSITE_ID'),
                'script_url' => env('UMAMI_SCRIPT_URL'),
            ],
            'vercel' => [
                'enabled' => filter_var(env('VERCEL_ANALYTICS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            ],
        ],
        'media' => [
            'youtube' => [
                'label' => 'YouTube embeds',
                'category' => 'media',
            ],
        ],
    ],
];
