<?php

namespace App\Enums\Notifications;

enum Status: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
    case READ = 'READ';
}
