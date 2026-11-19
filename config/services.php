<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        // Cuenta de servicio (JSON). Ruta absoluta o relativa a storage/app.
        'credentials' => env('GOOGLE_CREDENTIALS_PATH', 'google/credentials.json'),
        // Property ID numérico de GA4 (no el G-XXXX).
        'ga4_property_id' => env('GOOGLE_GA4_PROPERTY_ID'),
        // Propiedad de Search Console, ej. sc-domain:midominio.mx o https://midominio.mx/
        'search_console_site' => env('GOOGLE_SEARCH_CONSOLE_SITE'),
    ],
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'pro_model' => env('GEMINI_PRO_MODEL', 'gemini-2.5-pro'),
        'image_model' => env('GEMINI_IMAGE_MODEL', 'gemini-3.1-flash-image'),
    ],
    'contact' => [
        'email' => env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS')),
    ],

];
