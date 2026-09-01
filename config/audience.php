<?php

return [

    /*
    |--------------------------------------------------------------------------
    | First-party audience ping (consent tier T1)
    |--------------------------------------------------------------------------
    |
    | A cookieless page-count ping designed against the CNIL audience-
    | measurement exemption criteria: no cross-site identifier, no cookie,
    | truncated IP folded into a digest that rotates daily, and a client-side
    | opt-out plus server-side Global Privacy Control handling. It runs even
    | at 0% consent because it does not need any.
    |
    */

    'enabled' => (bool) env('AUDIENCE_ENABLED', true),

    // 'log' writes one structured line per ping to the app logger (Vercel
    // drains function logs; prod has no database). 'posthog' relays the
    // anonymous event server-side to the EU project configured in
    // config/consent.php.
    'sink' => env('AUDIENCE_SINK', 'log'),

    'endpoint' => '/audience',
];
