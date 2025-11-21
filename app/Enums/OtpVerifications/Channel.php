<?php

namespace App\Enums\OtpVerifications;

enum Channel: string
{
    case SMS = 'SMS';
    case EMAIL = 'EMAIL';
}
