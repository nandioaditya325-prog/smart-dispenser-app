<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Smart Dispenser Application Settings
    |--------------------------------------------------------------------------
    |
    | Default values used by PricingService/OrderService. Can be overridden
    | per-key at runtime via the `settings` table without a redeploy.
    |
    */

    'qris_expiry_seconds' => env('DISPENSER_QRIS_EXPIRY_SECONDS', 120),

    // Default price per milliliter (IDR), before any `settings` override.
    'price_per_ml' => [
        'normal' => env('DISPENSER_PRICE_PER_ML_NORMAL', 3),
        'dingin' => env('DISPENSER_PRICE_PER_ML_DINGIN', 5),
        'panas' => env('DISPENSER_PRICE_PER_ML_PANAS', 5),
    ],
];
