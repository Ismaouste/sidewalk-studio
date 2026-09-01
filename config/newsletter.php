<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Newsletter capture (funnel stage V3)
    |--------------------------------------------------------------------------
    |
    | Double opt-in through Brevo: the endpoint hands the address to Brevo's
    | doubleOptinConfirmation API, Brevo sends the confirmation email, and
    | the contact only enters a list after clicking it. The 'log' driver is
    | the zero-credential default: it records a masked address so a signup
    | is visible in function logs without a byte of PII.
    |
    */

    'driver' => env('NEWSLETTER_DRIVER', 'log'),

    'brevo' => [
        'key' => env('BREVO_API_KEY'),
        'doi_template_id' => (int) env('BREVO_DOI_TEMPLATE_ID', 0),
        'lists' => [
            'engineering' => (int) env('BREVO_LIST_ENGINEERING', 0),
            'local-business' => (int) env('BREVO_LIST_LOCAL_BUSINESS', 0),
        ],
    ],
];
