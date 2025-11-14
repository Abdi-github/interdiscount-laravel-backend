<?php

namespace App\Domain\Admin\Enums;

enum AdminType: string
{
    case SUPER_ADMIN = 'super_admin';
    case PLATFORM_ADMIN = 'platform_admin';
    case STORE_MANAGER = 'store_manager';
    case STORE_STAFF = 'store_staff';
    case SUPPORT_AGENT = 'support_agent';
}
