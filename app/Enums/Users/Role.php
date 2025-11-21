<?php

namespace App\Enums\Users;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case OUTLET_STAFF = 'OUTLET_STAFF';
    case MANAGER = 'MANAGER';
}
