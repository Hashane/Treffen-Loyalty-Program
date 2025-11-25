<?php

namespace App\Interfaces;

use App\Models\MembershipTier;
use App\Models\Transaction;

interface CalculatesLoyaltyPoints
{
    public function calculate(MembershipTier $tier, Transaction $transaction): int;
}
