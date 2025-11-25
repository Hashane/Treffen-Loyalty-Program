<?php

namespace App\Loyalty\Calculators;

use App\Interfaces\CalculatesLoyaltyPoints;
use App\Models\MembershipTier;
use App\Models\Transaction;

class StandardPointCalculator implements CalculatesLoyaltyPoints
{
    public function calculate(MembershipTier $tier, Transaction $transaction): int
    {
        return (int) round($transaction->total_amount * $tier->points_multiplier);
    }
}
