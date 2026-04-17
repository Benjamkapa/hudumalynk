<?php
/**
 * Shared application parameters
 */
return [
    'adminEmail'      => 'admin@hudumalynk.com',
    'supportEmail'    => 'support@hudumalynk.com',
    'senderEmail'     => 'noreply@hudumalynk.com',
    'senderName'      => 'HudumaLynk',
    'frontendUrl'     => $_ENV['FRONTEND_URL'] ?? 'http://hudumalynk.local',
    'backendUrl'      => $_ENV['BACKEND_URL'] ?? 'http://admin.hudumalynk.local',

    // Currency
    'defaultCurrency' => 'KES',
    'supportedCurrencies' => ['KES', 'USD'],
    'kesToUsdRate'    => (float)($_ENV['KES_TO_USD_RATE'] ?? 0.0077),

    // Subscription plans (seeded via migration — these are defaults)
    'subscriptionPlans' => [
        'basic'        => ['name' => 'Basic',        'price_kes' => 1000, 'duration_days' => 30, 'max_products' => 5,   'max_services' => 3,  'featured_slots' => 0],
        'professional' => ['name' => 'Professional', 'price_kes' => 2500, 'duration_days' => 30, 'max_products' => 20,  'max_services' => 10, 'featured_slots' => 1],
        'premium'      => ['name' => 'Premium',      'price_kes' => 5000, 'duration_days' => 30, 'max_products' => 999, 'max_services' => 999,'featured_slots' => 3],
    ],

    // Order payment thresholds (KES)
    'orderThresholds' => [
        'cod_max'         => 2000,   // anything below: payment on delivery allowed
        'deposit_max'     => 10000,  // 2001–10000: partial deposit required
        'deposit_percent' => 30,     // deposit = 30% of total
        // above 10000: full payment required
    ],

    // Commission rate (percent)
    'commissionRate' => 10,

    // Subscription grace period (days) before listings go inactive
    'subscriptionGraceDays' => 3,

    // Upload limits
    'maxUploadSize'    => 5242880,  // 5 MB
    'allowedImageTypes' => ['jpg', 'jpeg', 'png', 'webp'],
    'maxImagesPerListing' => 5,
];
