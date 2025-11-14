<?php

namespace App\Domain\Transfer\Enums;

enum TransferStatus: string
{
    case REQUESTED = 'REQUESTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case SHIPPED = 'SHIPPED';
    case RECEIVED = 'RECEIVED';
    case CANCELLED = 'CANCELLED';
}
