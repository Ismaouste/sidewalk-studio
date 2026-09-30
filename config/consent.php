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
            'description' => 'Audience measurement with Google Analytics 4 and product analytics via PostHog EU Cloud, loaded only after opt-in; nothing is sent to either before. Session replay and heatmaps stay behind their own explicit switch on the data-processing page.',
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
            // Google Analytics 4, for Search Console and Analytics reporting.
            // Consent Mode v2 *basic*: gtag.js is not loaded and nothing is
            // sent to Google before the `analytics` category is accepted.
            'ga4' => [
                'id' => env('GA_MEASUREMENT_ID'),
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
