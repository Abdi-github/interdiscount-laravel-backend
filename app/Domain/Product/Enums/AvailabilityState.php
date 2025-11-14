<?php

namespace App\Domain\Product\Enums;

enum AvailabilityState: string
{
    case INSTOCK = 'INSTOCK';
    case ONORDER = 'ONORDER';
    case RESERVATION = 'RESERVATION';
    case ELECTRONIC_SOFTWARE_DOWNLOAD_NOW = 'ELECTRONIC_SOFTWARE_DOWNLOAD_NOW';
    case OUT_OF_STOCK = 'OUT_OF_STOCK';
    case DISCONTINUED = 'DISCONTINUED';
}
