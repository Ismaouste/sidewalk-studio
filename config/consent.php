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
            'description' => 'Reserved for privacy-first metrics adapters such as Matomo or PostHog once the dedicated spec lands.',
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
        ],
        'media' => [
            'youtube' => [
                'label' => 'YouTube embeds',
                'category' => 'media',
            ],
        ],
    ],
];
