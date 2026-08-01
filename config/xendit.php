<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Xendit API Credentials & Endpoints
    |--------------------------------------------------------------------------
    |
    | Never hardcode the secret key. All values are sourced from the .env
    | file; see .env.example for the required keys.
    |
    */

    'secret_key' => env('XENDIT_SECRET_KEY'),

    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),

    // Verification token shown in the Xendit Dashboard -> Webhooks page,
    // sent back on every webhook call as the `x-callback-token` header.
    'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
];
