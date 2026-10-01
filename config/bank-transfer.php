<?php

/**
 * Bank Transfer Configuration for Pakistan
 *
 * This configuration stores bank account details for direct bank transfers.
 * Used for manual bank transfers and displays bank information to customers.
 */

return [
    /**
     * Primary Bank Account
     */
    'bank' => [
        'name' => env('BANK_NAME', 'HBL'),
        'account_title' => env('BANK_ACCOUNT_NAME', 'VandoraX (Private) Limited'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
        'iban' => env('BANK_IBAN', ''),
        'code' => env('BANK_CODE', 'HBL'),
        'swift_code' => env('BANK_SWIFT_CODE', 'HBLKPKK'),
        'branch' => env('BANK_BRANCH', 'Main Office'),
        'branch_code' => env('BANK_BRANCH_CODE', '0001'),
    ],

    /**
     * Additional Bank Accounts (optional)
     */
    'additional_accounts' => [
        // [
        //     'name' => 'UBL',
        //     'account_title' => 'VandoraX (Private) Limited',
        //     'account_number' => '1234567890',
        //     'iban' => 'PK67UBAF0000123456789',
        //     'swift_code' => 'UBLKPKK',
        // ],
    ],

    /**
     * Currency
     */
    'currency' => 'PKR',

    /**
     * Transfer Instructions
     */
    'instructions' => [
        'title' => 'Bank Transfer Instructions',
        'steps' => [
            '1. Open your bank app or visit your bank branch',
            '2. Select Fund Transfer or Send Money option',
            '3. Enter the account details provided above',
            '4. Enter the amount from your order summary',
            '5. Add reference: Your Order Number (ORD-XXXXXX-XXXXXX)',
            '6. Complete the transfer',
            '7. Your order will be confirmed once we receive the transfer',
        ],
    ],

    /**
     * Payment Verification
     */
    'verification' => [
        'enabled' => env('BANK_TRANSFER_AUTO_VERIFY', false),
        'timeout_hours' => env('BANK_TRANSFER_TIMEOUT', 48),
        'manual_review' => env('BANK_TRANSFER_MANUAL_REVIEW', true),
    ],

    /**
     * Notifications
     */
    'notifications' => [
        'notify_admin' => true,
        'notify_customer' => true,
        'admin_email' => env('BANK_TRANSFER_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS')),
    ],
];
