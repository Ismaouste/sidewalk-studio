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
            'description' => 'Product analytics via PostHog EU Cloud, loaded only after opt-in. Session replay and heatmaps stay behind their own explicit switch on the data-processing page.',
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
            'posthog' => [
                'key' => env('POSTHOG_KEY'),
                'host' => env('POSTHOG_HOST', 'https://eu.i.posthog.com'),
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
