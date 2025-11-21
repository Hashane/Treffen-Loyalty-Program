<?php

namespace App\Enums\Redemptions;

enum Status: string
{
    case PENDING = 'PENDING';
    case OTP_SENT = 'OTP_SENT';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
}
