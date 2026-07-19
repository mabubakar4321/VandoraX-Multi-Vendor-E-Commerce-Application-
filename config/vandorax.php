<?php

return[

// application name and version
    'app_name'=>'VandoraX',
    'version'=>'1.0.0',

   // Commission Settings

    'default_commission_rate'=> 15,
    'min_payout_amount'=> 5000,

    // return policy
    'return_window_days'=> 4,

    //quality control
    'bad_review_threshold'=> 10,

    // payout frequency
    'payout_frequency'=> 'weekly',

    // payment settings
    'payment_gateways'=> ['stripe','jazzcash'],

   // File Upload Limits

    'max_image_size'=> 5120,
    'max_document_size'=>10240,

    //  Pagination
    'per_page'=> 15,

    //currency
    'currency'=> 'PKR',
    'currency_symbol'=>'₨',

];


