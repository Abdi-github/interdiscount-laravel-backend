<?php

namespace App\Domain\Order\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case PAID = 'PAID';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
}
