<?php

namespace App\Enums\Referrals;

enum Status: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
