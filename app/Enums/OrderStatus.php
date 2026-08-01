<?php

namespace App\Enums;

/**
 * Lifecycle states of an Order, mirrored 1:1 with the `status` enum
 * column on the orders table.
 */
enum OrderStatus: string
{
    case Pending        = 'pending';
    case WaitingPayment  = 'waiting_payment';
    case Paid           = 'paid';
    case Expired        = 'expired';
    case Cancelled      = 'cancelled';
    case Failed         = 'failed';
    case Completed      = 'completed';
}
