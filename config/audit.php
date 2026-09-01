<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Automated CWV + SEO audit (lead magnet, funnel stage V3)
    |--------------------------------------------------------------------------
    |
    | One PageSpeed Insights v5 call per request (mobile strategy — the
    | positioning is mobile-first). The response already embeds CrUX field
    | data, so no second API call. Works keyless at low volume; the key
    | raises the quota to 25k/day and is recommended in production.
    |
    */

    'enabled' => (bool) env('AUDIT_ENABLED', true),

    'pagespeed' => [
        'endpoint' => 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
        'key' => env('PAGESPEED_API_KEY'),
        'timeout' => (int) env('PAGESPEED_TIMEOUT', 40),
    ],
];
