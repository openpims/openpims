<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Keys
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for Cloudflare Turnstile.
    | These credentials can be obtained from the Cloudflare dashboard.
    |
    */

    'site_key' => env('TURNSTILE_SITE_KEY', ''),
    'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
];