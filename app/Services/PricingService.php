<?php

namespace App\Services;

use App\Enums\WaterType;
use App\Models\Setting;

/**
 * Computes the nominal price (IDR) for a given water type and volume.
 * Base rates are configurable at runtime via the `settings` table so
 * prices can be adjusted without a redeploy; falls back to
 * config/dispenser.php defaults when a setting is absent.
 */
class PricingService
{
    /**
     * Price per milliliter (IDR), by water type.
     */
    public function calculate(WaterType $waterType, int $volumeMl): int
    {
        $pricePerMl = match ($waterType) {
            WaterType::Normal => (float) Setting::get('price_per_ml_normal', config('dispenser.price_per_ml.normal')),
            WaterType::Cold => (float) Setting::get('price_per_ml_cold', config('dispenser.price_per_ml.dingin')),
            WaterType::Hot => (float) Setting::get('price_per_ml_hot', config('dispenser.price_per_ml.panas')),
        };

        return (int) round($pricePerMl * $volumeMl);
    }
}
