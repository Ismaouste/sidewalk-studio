<?php

return [
    'name' => env('SITE_NAME', 'Sidewalk Studio'),
    'tagline' => env('SITE_TAGLINE', 'Spec-driven portfolio and lab for privacy-first web systems.'),
    'description' => env('SITE_DESCRIPTION', 'Engineering portfolio exploring privacy-first analytics, structured content systems, and refined Laravel + Inertia front-end craft.'),
    'locale' => 'en',
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'author' => [
        'name' => env('SITE_AUTHOR_NAME', 'Isma'),
        'job_title' => env('SITE_AUTHOR_TITLE', 'PHP Tech Lead and privacy-first web systems builder'),
        'email' => env('SITE_CONTACT_EMAIL', 'hello@sidewalk-studio.test'),
        'same_as' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('SITE_SAME_AS', 'https://github.com/Ismaosute,https://www.linkedin.com')),
        ))),
    ],
    'navigation' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'About', 'href' => '/about'],
        ['label' => 'Projects', 'href' => '/projects'],
        ['label' => 'Labs', 'href' => '/labs'],
        ['label' => 'Writing', 'href' => '/writing'],
        ['label' => 'Case Studies', 'href' => '/case-studies'],
        ['label' => 'Contact', 'href' => '/contact'],
    ],
    'labs' => [
        [
            'slug' => 'consent-sandbox',
            'title' => 'Consent Sandbox',
            'status' => 'Active',
            'summary' => 'A live area to validate cookie categories, iframe gating, and script orchestration before shipping analytics integrations.',
            'stack' => ['Laravel', 'Inertia', 'CookieConsent', 'IframeManager'],
        ],
        [
            'slug' => 'structured-data-playground',
            'title' => 'Structured Data Playground',
            'status' => 'Active',
            'summary' => 'An internal test surface for JSON-LD payloads, canonical decisions, and sitemap assumptions.',
            'stack' => ['Laravel', 'Schema.org', 'XML'],
        ],
        [
            'slug' => 'theme-experiments',
            'title' => 'Theme Experiments',
            'status' => 'Planned',
            'summary' => 'Reserved for the later theme-and-motion spec, once the content model and SEO shell are stable.',
            'stack' => ['Vue', 'Tailwind v4'],
        ],
    ],
    'contact' => [
        'email' => env('SITE_CONTACT_EMAIL', 'hello@sidewalk-studio.test'),
        'location' => 'France',
        'availability' => 'Open to lead developer, freelance modernization, and privacy/SEO architecture conversations.',
    ],
];
