<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "reverb", "pusher", "ably", "mercure", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_CONNECTION', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over WebSockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_HOST'),
                'port' => env('REVERB_PORT', 443),
                'scheme' => env('REVERB_SCHEME', 'https'),
                'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'mercure' => [
            'driver' => 'mercure',
            'url' => env('MERCURE_URL'),
            'public_url' => env('MERCURE_PUBLIC_URL'),
            'secret' => env('MERCURE_JWT_SECRET'),
            'claims' => [
                'iss' => env('MERCURE_JWT_ISSUER'),
                'client_id' => env('APP_NAME'),
                // "aud" defaults to public_url (or url); "sub" defaults to
                // "anonymous" for guests on the subscriber side and to
                // "client_id" on the publisher side. An authenticated user's
                // id always overrides "sub" on their own subscriber token
                // (see MercureBroadcaster::makeToken()).
            ],
            // The spec's default cookie name ("__Secure-mercure_access_token")
            // carries the "__Secure-" prefix, which browsers (and curl) refuse
            // to store without the Secure attribute — i.e. without HTTPS. Leave
            // this unset in any HTTPS deployment; it exists only so local dev
            // over plain HTTP still gets a cookie at all.
            'cookie_name' => env('MERCURE_COOKIE_NAME'),

            // Everything below is optional; the values shown are the defaults.

            // Subscriber-token/cookie lifetime, in minutes. Keep it short:
            // it also bounds how long a revoked user keeps receiving events
            // (there is no token-revocation mechanism). Echo's connector
            // re-mints the cookie automatically before it expires.
            'subscribe_expiration' => (int) env('MERCURE_SUBSCRIBE_EXPIRATION', 5),

            // Publish-token lifetime, in minutes. 0 lets the component pick
            // (php.ini session.cookie_lifetime, or 3600 seconds). The token
            // is cached and reused until it nears expiry.
            // 'publish_expiration' => 0,

            // JWT signing algorithm; see LcobucciFactory::SIGN_ALGORITHMS
            // ("hmac.*", "rsa.*", "ecdsa.*").
            // 'algorithm' => 'hmac.sha256',
            // 'passphrase' => '',

            // Any of secret/algorithm/passphrase can be set per side, which
            // limits the blast radius of a leaked subscriber key — worth it
            // when the hub is operated by a third party.
            // 'subscribe_secret' => env('MERCURE_SUBSCRIBER_JWT_SECRET'),
            // 'subscribe_algorithm' => 'hmac.sha256',
            // 'subscribe_passphrase' => '',
            // 'publish_secret' => env('MERCURE_PUBLISHER_JWT_SECRET'),
            // 'publish_algorithm' => 'hmac.sha256',
            // 'publish_passphrase' => '',

            // Options for the Symfony HttpClient used to publish updates
            // (https://symfony.com/doc/current/http_client.html): set a
            // timeout so an unreachable hub can't stall queue workers.
            // 'client_options' => ['timeout' => 10],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
