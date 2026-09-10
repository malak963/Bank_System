<?php

return [
    'name' => env('BANK_NAME', 'Bank System'),
    'short_name' => env('BANK_SHORT_NAME', 'BS'),
    'tagline' => env('BANK_TAGLINE', 'Core Banking Platform'),
    'descriptor' => env('BANK_DESCRIPTOR', 'Secure banking operations'),
    'currency' => env('BANK_CURRENCY', 'SYP'),
    'currency_decimals' => 2,
    'support_email' => env('BANK_SUPPORT_EMAIL', 'support@example.com'),
    'dashboard' => [
        'eyebrow' => 'Core banking overview',
        'title' => 'Institutional banking, clearly managed.',
        'description' => 'Monitor liquidity, credit decisions and upcoming collections from one focused workspace.',
        'pulse_label' => 'Portfolio pulse',
    ],
];
