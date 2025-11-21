<?php

namespace App\Enums\Notifications;

enum NotificationType: string
{
    case POINTS_EARNED = 'POINTS_EARNED';
    case POINTS_EXPIRING = 'POINTS_EXPIRING';
    case POINTS_EXPIRED = 'POINTS_EXPIRED';
    case TIER_UPGRADED = 'TIER_UPGRADED';
    case REFERRAL_BONUS = 'REFERRAL_BONUS';
    case REDEMPTION_CONFIRMED = 'REDEMPTION_CONFIRMED';
    case WELCOME = 'WELCOME';
    case PASSWORD_RESET = 'PASSWORD_RESET';
    case OTHER = 'OTHER';
}
