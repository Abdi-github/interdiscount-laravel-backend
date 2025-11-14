<?php

namespace App\Domain\Product\Enums;

enum ProductStatus: string
{
    case PUBLISHED = 'PUBLISHED';
    case DRAFT = 'DRAFT';
    case INACTIVE = 'INACTIVE';
    case ARCHIVED = 'ARCHIVED';
}
