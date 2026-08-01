<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'price_per_ml_normal',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Harga per ml untuk air normal (IDR)',
            ],
            [
                'key' => 'price_per_ml_cold',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Harga per ml untuk air dingin (IDR)',
            ],
            [
                'key' => 'price_per_ml_hot',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Harga per ml untuk air panas (IDR)',
            ],
            [
                'key' => 'qris_expiry_seconds',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Masa berlaku QRIS dinamis sebelum kedaluwarsa (detik)',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
