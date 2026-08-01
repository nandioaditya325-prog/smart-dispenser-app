<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        Device::query()->updateOrCreate(
            ['device_id' => 'DISPENSER001'],
            [
                'name' => 'Smart Dispenser - Lobby',
                'location' => 'Kantor Pusat Lt. 1',
                'firmware_version' => '1.0.0',
                'is_online' => false,
                'mqtt_connected' => false,
            ]
        );
    }
}
