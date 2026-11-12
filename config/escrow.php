<?php

return [
    'account' => [
        'holder' => env('ESCROW_ACCOUNT_HOLDER', 'VandoraX Private Limited - Escrow'),
        'bank_name' => env('ESCROW_BANK_NAME', 'HBL'),
        'account_number' => env('ESCROW_ACCOUNT_NUMBER', ''),
        'iban' => env('ESCROW_IBAN', ''),
        'swift_code' => env('ESCROW_SWIFT_CODE', 'HBLKPKK'),
        'branch' => env('ESCROW_BRANCH', 'Escrow Branch Karachi'),
    ],
    'restrictions' => [
        'withdrawal_requires_approval' => env('ESCROW_WITHDRAWAL_REQUIRES_APPROVAL', true),
        'auto_release_days' => (int) env('ESCROW_AUTO_RELEASE_DAYS', 0),
        'min_payout_amount' => (int) env('ESCROW_MIN_PAYOUT_AMOUNT', 5000),
    ],
    'statuses' => ['held', 'released', 'refunded', 'disputed'],
];
