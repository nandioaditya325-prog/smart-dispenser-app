<?php

namespace App\Enums;

/**
 * Water type requested by the device, matching the ESP32 firmware's
 * waterTypeToString() mapping exactly.
 */
enum WaterType: string
{
    case Normal = 'normal';
    case Cold   = 'dingin';
    case Hot    = 'panas';
}
