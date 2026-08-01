<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid_uid',
        'water_type',
        'volume_ml',
        'price',
        'payment_status',
    ];
}