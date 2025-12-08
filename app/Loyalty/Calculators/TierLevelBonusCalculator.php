<?php

namespace App\Loyalty\Calculators;

use App\Interfaces\CalculatesLoyaltyPoints;
use App\Models\MembershipTier;
use App\Models\Transaction;

class TierLevelBonusCalculator implements CalculatesLoyaltyPoints
{
    public function calculate(MembershipTier $tier, Transaction $transaction): int
    {
        $basePoints = $transaction->total_amount * $tier->points_multiplier;

        // tier bonus
        $totalPoints = $basePoints + $tier->tier_bonus;

        return (int) round($totalPoints);
    }
}
