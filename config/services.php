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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
        'anon_key' => env('SUPABASE_ANON_KEY'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'price_id' => env('STRIPE_PRICE_ID'),
        'product_id' => env('STRIPE_PRODUCT_ID'),
    ],

    'digitalocean' => [
        'key' => env('DIGITALOCEAN_SPACES_KEY'),
        'secret' => env('DIGITALOCEAN_SPACES_SECRET'),
        'region' => env('DIGITALOCEAN_SPACES_REGION', 'nyc3'),
        'endpoint' => env('DIGITALOCEAN_SPACES_ENDPOINT', 'https://nyc3.digitaloceanspaces.com'),
        'bucket_name' => env('DIGITALOCEAN_SPACES_BUCKET', 'hazel-audio-clips'),
        'base_path' => env('DIGITALOCEAN_SPACES_BASE_PATH', 'livekit/audio_transcripts'),
    ],

];
