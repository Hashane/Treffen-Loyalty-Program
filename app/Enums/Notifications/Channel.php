<?php

namespace App\Enums\Notifications;

enum Channel: string
{
    case EMAIL = 'EMAIL';
    case SMS = 'SMS';
    case PUSH = 'PUSH';
    case IN_APP = 'IN_APP';
}
