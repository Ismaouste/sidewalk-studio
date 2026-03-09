<?php

return [
    'settings_source' => env('SITE_SETTINGS_SOURCE', 'files'),
    'name' => env('SITE_NAME', 'Ismael Rodmacq'),
    'tagline' => env('SITE_TAGLINE', 'Fullstack ecommerce. Cross-functional data, reliable flows.'),
    'description' => env('SITE_DESCRIPTION', 'Fullstack web development for ecommerce, product-data flows, CMS delivery, consent-aware tracking, and technical SEO.'),
    'locale' => 'en',
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'author' => [
        'name' => env('SITE_AUTHOR_NAME', 'Ismael Rodmacq'),
        'job_title' => env('SITE_AUTHOR_TITLE', 'Fullstack ecommerce developer for product data, integrations, tracking, and technical SEO'),
        'email' => env('SITE_CONTACT_EMAIL', 'ismael@rodmacq.com'),
        'same_as' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('SITE_SAME_AS', 'https://github.com/Ismaouste,https://www.linkedin.com/in/ismaelrodmacq')),
        ))),
    ],
    'navigation' => [
        ['label' => 'Hello', 'href' => '/'],
        ['label' => 'Experience', 'href' => '/projects'],
        ['label' => 'Journal', 'href' => '/journal'],
        ['label' => 'Contact ✍🏽', 'href' => '/contact'],
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
        'email' => env('SITE_CONTACT_EMAIL', 'ismael@rodmacq.com'),
        'location' => 'Nancy, Grand-Est',
        'availability' => 'Currently working at Jewely and open to conversations, part-time freelance work, and new opportunities.',
    ],
];
