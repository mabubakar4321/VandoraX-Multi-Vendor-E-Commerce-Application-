<?php

return [
    'secret' => env('STRIPE_SECRET'),
    'publishable_key' => env('STRIPE_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'currency' => strtolower(env('STRIPE_CURRENCY', 'pkr')),
    'description' => env('STRIPE_DESCRIPTION', 'VandoraX order payment'),
    'timeout' => (int) env('STRIPE_PAYMENT_TIMEOUT', 300),
    'rate_limit' => [
        'attempts' => (int) env('STRIPE_RATE_LIMIT_ATTEMPTS', 5),
        'decay_minutes' => (int) env('STRIPE_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
    'webhook_tolerance' => 300,
    'test_cards' => [
        'success' => '4242424242424242',
        'declined' => '4000000000000002',
        'requires_auth' => '4000002500003155',
    ],
];
