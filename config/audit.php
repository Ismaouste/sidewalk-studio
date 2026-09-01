<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Automated CWV + SEO audit (lead magnet, funnel stage V3)
    |--------------------------------------------------------------------------
    |
    | One PageSpeed Insights v5 call per request (mobile strategy — the
    | positioning is mobile-first). The response already embeds CrUX field
    | data, so no second API call. In practice the keyless quota is zero
    | (observed: instant 429), so the free API key (25k/day) is required
    | for the tool to answer; without it the endpoint degrades to a clean
    | 502 "unavailable" and the page says try again later.
    |
    */

    'enabled' => (bool) env('AUDIT_ENABLED', true),

    'pagespeed' => [
        'endpoint' => 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
        'key' => env('PAGESPEED_API_KEY'),
        'timeout' => (int) env('PAGESPEED_TIMEOUT', 40),
    ],
];
