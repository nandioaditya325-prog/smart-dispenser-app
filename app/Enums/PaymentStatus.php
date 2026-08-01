<?php

namespace App\Enums;

/**
 * Payment status as reported by Xendit, mirrored on the payments table.
 */
enum PaymentStatus: string
{
    case Pending = 'PENDING';
    case Active  = 'ACTIVE';
    case Paid    = 'PAID';
    case Expired = 'EXPIRED';
    case Failed  = 'FAILED';
}
