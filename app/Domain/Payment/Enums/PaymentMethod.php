<?php

namespace App\Domain\Payment\Enums;

enum PaymentMethod: string
{
    case CARD = 'card';
    case TWINT = 'twint';
    case POSTFINANCE = 'postfinance';
    case INVOICE = 'invoice';
}
