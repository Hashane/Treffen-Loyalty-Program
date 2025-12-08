<?php

namespace App\Loyalty\Calculators;

use App\Interfaces\CalculatesLoyaltyPoints;
use App\Models\Member;
use App\Models\MembershipTier;
use App\Models\Transaction;

class BenefitsAwareCalculator implements CalculatesLoyaltyPoints
{
    public function calculate(MembershipTier $tier, Transaction $transaction): int
    {
        $benefits = $tier->benefits ?? [];

        // Base points from room booking
        $points = $transaction->total_amount * $tier->points_multiplier;

        // 1. Bonus per night of stay
        if (isset($benefits['points_per_night_bonus']) && $transaction->nights) {
            $points += $benefits['points_per_night_bonus'] * $transaction->nights;
        }

        // 2. Service-specific bonuses (dining, spa, etc.)
        if (isset($benefits['service_bonuses'][$transaction->department])) {
            $points += $benefits['service_bonuses'][$transaction->department];
        }

        // 3. Minimum nights threshold bonus
        if (isset($benefits['stay_threshold_bonus'])) {
            $threshold = $benefits['stay_threshold_bonus'];
            if ($transaction->nights >= $threshold['nights']) {
                $points += $threshold['bonus'];
            }
        }

        // 4. Weekend stay multiplier
        if (isset($benefits['weekend_multiplier'])) {
            if ($this->isWeekendStay($transaction)) {
                $points *= $benefits['weekend_multiplier'];
            }
        }

        // 5. Seasonal bonuses
        if (isset($benefits['seasonal_bonus'])) {
            $points *= $this->getSeasonalMultiplier($benefits['seasonal_bonus']);
        }

        // 6. Birthday bonus
        if (isset($benefits['birthday_bonus'])) {
            if ($this->isBirthdayMonth($transaction->member)) {
                $points += $benefits['birthday_bonus']['points'];
            }
        }

        return (int) round($points);
    }

    protected function isWeekendStay(Transaction $transaction): bool
    {
        // TODO: Implement when check_in_date and check_out_date fields are added
        if (! isset($transaction->check_in_date) || ! isset($transaction->check_out_date)) {
            return false;
        }

        return $transaction->check_in_date->isWeekend() ||
            $transaction->check_out_date->isWeekend();
    }

    protected function getSeasonalMultiplier(array $seasonalBonus): float
    {
        $month = now()->month;

        // Holiday season (Nov-Dec)
        if (in_array($month, [11, 12]) && isset($seasonalBonus['holiday_season'])) {
            return $seasonalBonus['holiday_season'];
        }

        // Summer (Jun-Aug)
        if (in_array($month, [6, 7, 8]) && isset($seasonalBonus['summer'])) {
            return $seasonalBonus['summer'];
        }

        return 1.0;
    }

    protected function isBirthdayMonth(Member $member): bool
    {
        if (! $member->date_of_birth) {
            return false;
        }

        return $member->date_of_birth->month === now()->month;
    }
}
