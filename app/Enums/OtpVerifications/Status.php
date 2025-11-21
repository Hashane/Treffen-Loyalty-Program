<?php

namespace App\Enums\OtpVerifications;

enum Status: string
{
    case PENDING = 'PENDING';
    case VERIFIED = 'VERIFIED';
    case EXPIRED = 'EXPIRED';
    case FAILED = 'FAILED';
}
