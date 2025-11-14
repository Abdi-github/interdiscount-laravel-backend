<?php

namespace App\Domain\Payment\Enums;

enum PaymentTransactionStatus: string
{
    case PENDING = 'PENDING';
    case SUCCEEDED = 'SUCCEEDED';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
}
