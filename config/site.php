<?php

/**
 * Structure, not identity.
 *
 * Who the site is about lives in two places only: `lang/{locale}/site.php`,
 * which the operator edits from /admin/language-files, and
 * `database/seeders/data/site-settings.json`, which seeds a fresh install.
 * `SiteSettingsService::defaultPayload()` reads the first and falls back to
 * the values here, so anything here is what a clone of this repository shows
 * before anyone has filled it in — a working site belonging to nobody.
 *
 * That is what makes `SiteIsAgnosticTest` able to say something: the owner's
 * name appearing anywhere outside those two files is a defect, and the test
 * fails on it.
 */
return [
    'settings_source' => env('SITE_SETTINGS_SOURCE', 'files'),

    /**
     * `files` or `database` — which source wins when both hold the same page
     * or publication. See `App\Content\ContentSource` for what the choice
     * means and why it is a setting rather than an edit.
     *
     * The default is `database`, which is what makes an edit saved from
     * /admin appear on the public site. Either source falls back to the other
     * for what it does not hold, so a deployment with no database — the
     * Vercel one, where SQLite is not in the repository — serves the Markdown
     * exactly as before and is unaffected by this default.
     */
    'content_source' => env('SITE_CONTENT_SOURCE', 'database'),
    'name' => env('SITE_NAME', 'Sidewalk Studio'),
    'tagline' => env('SITE_TAGLINE', 'Fullstack ecommerce. Cross-functional data, reliable flows.'),
    'description' => env('SITE_DESCRIPTION', 'Fullstack web development for ecommerce, product-data flows, CMS delivery, consent-aware tracking, and technical SEO.'),
    'locale' => 'en',
    'url' => rtrim(env('SITE_PUBLIC_URL', env('APP_URL', 'http://localhost')), '/'),
    'url_placeholder' => env('SITE_URL_PLACEHOLDER', '{{site_url}}'),
    'repository_url' => env('SITE_REPOSITORY_URL', 'https://github.com/Ismaouste/sidewalk-studio'),

    /**
     * The CV, addressed by where it is rather than by whose it is. What the
     * visitor's browser saves it as is built from the site identity at
     * request time — see `App\Support\CareerAsset`.
     */
    'cv' => [
        'directory' => env('SITE_CV_DIRECTORY', 'docs/career/output'),
        'filename' => env('SITE_CV_FILENAME', 'cv-{locale}.pdf'),
    ],
    'author' => [
        'name' => env('SITE_AUTHOR_NAME', 'Sidewalk Studio'),
        'job_title' => env('SITE_AUTHOR_TITLE', 'Full Stack Developer — E-commerce & Product Data'),
        'email' => env('SITE_CONTACT_EMAIL', 'hello@sidewalk-studio.test'),
        'same_as' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('SITE_SAME_AS', '')),
        ))),
    ],
    /**
     * The commercial spine: the offer and the proof sit in the menu, the
     * résumé record stepped back to the footer. Labels are localized at
     * render by `PublicLocale::navigation()`; these are the structural
     * defaults a fork starts from.
     */
    'navigation' => [
        ['label' => 'Hello', 'href' => '/'],
        ['label' => 'Services', 'href' => '/services'],
        ['label' => 'Case studies', 'href' => '/case-studies'],
        ['label' => 'Journal', 'href' => '/journal'],
        ['label' => 'Contact ✍🏽', 'href' => '/contact'],
    ],
    // The labs registry: structure and proper nouns only. Titles, summaries
    // and status labels live in lang/{locale}/public.php under `labs`, keyed
    // by these slugs, so the page renders in the resolved locale.
    'labs' => [
        [
            'slug' => 'cwv-audit',
            'status' => 'active',
            'stack' => ['Laravel', 'PageSpeed Insights', 'CrUX', 'Markdown mail'],
            'href' => '/labs/audit',
        ],
        [
            'slug' => 'consent-sandbox',
            'status' => 'active',
            'stack' => ['Laravel', 'Inertia', 'CookieConsent', 'IframeManager'],
        ],
        [
            'slug' => 'structured-data-playground',
            'status' => 'active',
            'stack' => ['Laravel', 'Schema.org', 'XML'],
        ],
        [
            'slug' => 'theme-experiments',
            'status' => 'planned',
            'stack' => ['Vue', 'Tailwind v4'],
        ],
    ],
    'contact' => [
        'email' => env('SITE_CONTACT_EMAIL', 'ismael@rodmacq.com'),
        'location' => 'Nancy, Grand Est',
        'availability' => 'Currently working at Jewely and open to conversations, part-time freelance work, and new opportunities.',
    ],
];
