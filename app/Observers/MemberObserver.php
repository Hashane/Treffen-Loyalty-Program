<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\MembershipTier;
use Illuminate\Support\Str;

class MemberObserver
{
    public function creating(Member $member): void
    {
        if (!$member->member_number) {
            $member->member_number = $this->generateUniqueMemberNumber();
        }

        if (!$member->referral_code) {
            $member->referral_code = $this->generateUniqueReferralCode();
        }

        if (!$member->membership_tier_id) {
            $defaultTier = MembershipTier::orderBy('tier_level')->first();

            if (!$defaultTier) {
                throw new \RuntimeException('No membership tiers exist. Please seed tiers first.');
            }

            $member->membership_tier_id = $defaultTier->id;
        }
    }

    private function generateUniqueMemberNumber(): string
    {
        $attempts = 0;
        $maxAttempts = 10;

        do {
            $memberNumber = 'MBR' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Failed to generate unique member number after ' . $maxAttempts . ' attempts');
            }
        } while (Member::where('member_number', $memberNumber)->exists());

        return $memberNumber;
    }

    private function generateUniqueReferralCode(): string
    {
        $attempts = 0;
        $maxAttempts = 10;

        do {
            $referralCode = strtoupper(Str::random(3) . rand(100, 999));
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Failed to generate unique referral code after ' . $maxAttempts . ' attempts');
            }
        } while (Member::where('referral_code', $referralCode)->exists());

        return $referralCode;
    }
}