<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Connections (php-mqtt/laravel-client)
    |--------------------------------------------------------------------------
    */

    'default_connection' => 'default',

    'connections' => [
        'default' => [
            'host' => env('MQTT_HOST', 'mqtt.smartdispenser.local'),
            'port' => env('MQTT_PORT', 8883),
            'protocol' => '3',            
            'client_id' => env('MQTT_CLIENT_ID_PREFIX', 'laravel-backend').'-'.uniqid(),
            'auto_connect' => true,
            'auto_close' => true,

            'connect_timeout' => 10,
            'socket_timeout' => 10,
            'resend_timeout' => 10,
            'keep_alive_interval' => 30,

            'auth' => [
                'username' => env('MQTT_USERNAME'),
                'password' => env('MQTT_PASSWORD'),
            ],

            'use_tls' => env('MQTT_USE_TLS', true),
            'tls' => [
                'verify_peer' => env('MQTT_TLS_VERIFY_PEER', true),
                'verify_peer_name' => env('MQTT_TLS_VERIFY_PEER_NAME', true),
                'self_signed_allowed' => env('MQTT_TLS_ALLOW_SELF_SIGNED', false),
                'ca_file' => env('MQTT_TLS_CA_FILE'),
            ],

            'last_will' => [
                'topic' => 'smartdispenser/status',
                'message' => json_encode(['backend' => 'offline']),
                'quality_of_service' => 1,
                'retain' => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application-Level Topic Names
    |--------------------------------------------------------------------------
    */
    'topics' => [
        'status' => 'smartdispenser/status',
        'payment' => 'smartdispenser/payment',
        'transaction' => 'smartdispenser/transaction',
        'flow' => 'smartdispenser/flow',
        'device' => 'smartdispenser/device',
        'error' => 'smartdispenser/error',
    ],
];
