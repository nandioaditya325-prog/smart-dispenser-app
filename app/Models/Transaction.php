<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'device_id',
        'rfid_uid',
        'water_type',
        'volume_ml',
        'price',
        'payment_status',
    ];
}