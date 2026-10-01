<?php

/**
 * JazzCash Payment Gateway Configuration
 *
 * JazzCash is a leading mobile payment gateway in Pakistan.
 * This configuration handles JazzCash integration for VandoraX.
 */

return [
    /**
     * Merchant Credentials
     */
    'merchant_id' => env('JAZZCASH_MERCHANT_ID', ''),
    'password' => env('JAZZCASH_PASSWORD', ''),
    'account_id' => env('JAZZCASH_ACCOUNT_ID', ''),
    'secret_key' => env('JAZZCASH_SECRET_KEY', ''),

    /**
     * Environment
     * Set to 'sandbox' for testing, 'production' for live
     */
    'environment' => env('JAZZCASH_ENVIRONMENT', 'sandbox'),

    /**
     * URLs
     */
    'urls' => [
        'sandbox' => 'https://sandbox.jazzcash.com.pk',
        'production' => 'https://www.jazzcash.com.pk',
    ],

    /**
     * API Endpoints
     */
    'endpoints' => [
        'initiate' => '/ApplicationAPI/API/2.0/Initiate',
        'complete' => '/ApplicationAPI/API/2.0/Complete',
        'query' => '/ApplicationAPI/API/2.0/QueryTransaction',
    ],

    /**
     * Currency
     */
    'currency' => 'PKR',

    /**
     * Default Timeout (in seconds)
     */
    'timeout' => 30,

    /**
     * Return URLs
     */
    'return_url' => env('JAZZCASH_RETURN_URL', env('APP_URL') . '/payment/jazzcash/callback'),
    'notify_url' => env('JAZZCASH_NOTIFY_URL', env('APP_URL') . '/webhooks/jazzcash'),

    /**
     * Supported Payment Methods
     */
    'payment_methods' => [
        'easypaisa' => 'EasyPaisa',
        'jazzcash' => 'JazzCash',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
    ],
];
