<?php

namespace App\Services;

use App\Enums\PointsLedger\PointsType;
use App\Models\Member;
use App\Models\PointsLedger;
use Illuminate\Support\Facades\DB;

class PointsLedgerService
{
    /**
     * Add points to member's account
     */
    public function addPoints(
        int $memberId,
        int $pointsChange,
        PointsType $pointsType,
        string $description,
        ?int $transactionId = null,
        ?\DateTime $expiryDate = null
    ): PointsLedger {
        return DB::transaction(function () use ($memberId, $pointsChange, $pointsType, $description, $transactionId, $expiryDate) {
            $member = Member::lockForUpdate()->findOrFail($memberId);
            $ledger = PointsLedger::create([
                'member_id' => $memberId,
                'transaction_id' => $transactionId,
                'points_change' => $pointsChange,
                'points_type' => $pointsType,
                'current_points_after' => $member->current_points + $pointsChange,
                'lifetime_points_after' => $member->lifetime_points + $pointsChange,
                'expiry_date' => $expiryDate,
                'description' => $description,
            ]);

            $member->increment('current_points', $pointsChange);
            $member->increment('lifetime_points', $pointsChange);

            return $ledger;
        });
    }

    /**
     * Deduct points (for redemptions)
     */
    public function deductPoints(
        int $memberId,
        int $pointsToDeduct,
        int $redemptionId,
        string $description = 'Points redeemed'
    ): PointsLedger {
        return DB::transaction(function () use ($memberId, $pointsToDeduct, $redemptionId, $description) {
            $member = Member::lockForUpdate()->findOrFail($memberId);

            if ($member->current_points < $pointsToDeduct) {
                throw new \Exception('Insufficient points balance');
            }

            $ledger = PointsLedger::create([
                'member_id' => $memberId,
                'redemption_id' => $redemptionId,
                'points_change' => -$pointsToDeduct,
                'points_type' => PointsType::REDEEMED,
                'current_points_after' => $member->current_points - $pointsToDeduct,
                'lifetime_points_after' => $member->lifetime_points,
                'description' => $description,
            ]);

            $member->decrement('current_points', $pointsToDeduct);

            return $ledger;
        });
    }

    /**
     * Expire points based on expiry date
     */
    public function expirePoints(int $memberId): int
    {
        return DB::transaction(function () use ($memberId) {
            $member = Member::lockForUpdate()->findOrFail($memberId);

            $expiredPoints = PointsLedger::where('member_id', $memberId)
                ->where('expired', false)
                ->where('expiry_date', '<=', now())
                ->where('points_change', '>', 0)
                ->sum('points_change');

            if ($expiredPoints > 0) {
                // Mark as expired
                PointsLedger::where('member_id', $memberId)
                    ->where('expired', false)
                    ->where('expiry_date', '<=', now())
                    ->update(['expired' => true]);

                // Create expiry ledger entry
                PointsLedger::create([
                    'member_id' => $memberId,
                    'points_change' => -$expiredPoints,
                    'points_type' => PointsType::EXPIRED,
                    'current_points_after' => $member->current_points - $expiredPoints,
                    'lifetime_points_after' => $member->lifetime_points,
                    'description' => 'Points expired',
                ]);

                $member->decrement('current_points', $expiredPoints);
            }

            return $expiredPoints;
        });
    }

    /**
     * Adjust points (admin correction)
     */
    public function adjustPoints(
        int $memberId,
        int $pointsChange,
        int $adjustedBy,
        string $reason
    ): PointsLedger {
        return DB::transaction(function () use ($memberId, $pointsChange, $adjustedBy, $reason) {
            $member = Member::lockForUpdate()->findOrFail($memberId);

            $ledger = PointsLedger::create([
                'member_id' => $memberId,
                'points_change' => $pointsChange,
                'points_type' => PointsType::ADJUSTED,
                'current_points_after' => $member->current_points + $pointsChange,
                'lifetime_points_after' => $member->lifetime_points + abs($pointsChange),
                'description' => 'Manual adjustment',
                'adjusted_by' => $adjustedBy,
                'adjustment_reason' => $reason,
            ]);

            $member->increment('current_points', $pointsChange);
            if ($pointsChange > 0) {
                $member->increment('lifetime_points', $pointsChange);
            }

            return $ledger;
        });
    }
}
