<?php

namespace App\Enums\PointsLedger;

enum PointsType: string
{
    case EARNED = 'EARNED';
    case REDEEMED = 'REDEEMED';
    case EXPIRED = 'EXPIRED';
    case ADJUSTED = 'ADJUSTED';
    case REFERRAL_BONUS = 'REFERRAL_BONUS';
    case REGISTRATION_BONUS = 'REGISTRATION_BONUS';
}
