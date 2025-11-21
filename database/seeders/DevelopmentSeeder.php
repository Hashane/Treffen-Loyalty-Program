<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MembershipTier;
use App\Models\Notification;
use App\Models\Outlet;
use App\Models\OutletType;
use App\Models\PointsLedger;
use App\Models\Redemption;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\RewardCategory;
use App\Models\TierHistory;
use App\Models\Transaction;
use App\Models\TransactionLineItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create base/lookup data
        $this->command->info('Creating membership tiers...');
        $tiers = MembershipTier::factory()->count(4)->create();

        $this->command->info('Creating outlet types...');
        $outletTypes = OutletType::factory()->count(3)->create();

        $this->command->info('Creating reward categories...');
        $rewardCategories = RewardCategory::factory()->count(5)->create();

        $this->command->info('Creating admin users...');
        $users = User::factory()->count(3)->create();

        // Step 2: Create entities that depend on base data
        $this->command->info('Creating outlets...');
        $outlets = Outlet::factory()->count(10)->create();

        $this->command->info('Creating members...');
        $members = Member::factory()->count(50)->create();

        $this->command->info('Creating rewards...');
        $rewards = Reward::factory()->count(20)->create();

        // Step 3: Create tier history for members
        $this->command->info('Creating tier history...');
        foreach ($members as $member) {
            TierHistory::factory()->count(rand(1, 3))->create([
                'member_id' => $member->id,
            ]);
        }

        // Step 4: Create transactions and their line items
        $this->command->info('Creating transactions...');
        foreach ($members->random(30) as $member) {
            $transactions = Transaction::factory()->count(rand(1, 5))->create([
                'member_id' => $member->id,
                'outlet_id' => $outlets->random()->id,
            ]);

            // Create line items for each transaction
            foreach ($transactions as $transaction) {
                TransactionLineItem::factory()->count(rand(1, 4))->create([
                    'transaction_id' => $transaction->id,
                ]);
            }

            // Create points ledger entries for earning points
            foreach ($transactions as $transaction) {
                PointsLedger::factory()->create([
                    'member_id' => $member->id,
                    'transaction_id' => $transaction->id,
                ]);
            }
        }

        // Step 5: Create redemptions and their points ledger entries
        $this->command->info('Creating redemptions...');
        foreach ($members->random(20) as $member) {
            $redemptions = Redemption::factory()->count(rand(1, 2))->create([
                'member_id' => $member->id,
                'reward_id' => $rewards->random()->id,
                'outlet_id' => $outlets->random()->id,
            ]);

            // Create points ledger entries for redemptions (deduction)
            foreach ($redemptions as $redemption) {
                PointsLedger::factory()->create([
                    'member_id' => $member->id,
                    'redemption_id' => $redemption->id,
                    'transaction_id' => null,
                ]);
            }
        }

        // Step 6: Create referrals with points ledger entries
        $this->command->info('Creating referrals...');
        $availableMembers = $members->shuffle();
        for ($i = 0; $i < 15; $i++) {
            $referrer = $availableMembers->shift();
            $referred = $availableMembers->shift();

            if ($referrer && $referred) {
                // Create points ledger for referral bonus
                $pointsLedger = PointsLedger::factory()->create([
                    'member_id' => $referrer->id,
                    'transaction_id' => null,
                    'redemption_id' => null,
                ]);

                // Create referral linked to points ledger
                Referral::factory()->create([
                    'referrer_member_id' => $referrer->id,
                    'referred_member_id' => $referred->id,
                    'points_ledger_id' => $pointsLedger->id,
                ]);
            }
        }

        // Step 7: Create some manual adjustments to points
        $this->command->info('Creating manual point adjustments...');
        foreach ($members->random(10) as $member) {
            PointsLedger::factory()->create([
                'member_id' => $member->id,
                'transaction_id' => null,
                'redemption_id' => null,
                'adjusted_by' => $users->random()->id,
            ]);
        }

        // Step 8: Create notifications
        $this->command->info('Creating notifications...');
        foreach ($members as $member) {
            Notification::factory()->count(rand(0, 5))->create([
                'member_id' => $member->id,
            ]);
        }

        // Step 9: Create OTP verifications for some redemptions
        $this->command->info('Creating OTP verifications...');
        $redemptions = \App\Models\Redemption::inRandomOrder()->limit(10)->get();
        foreach ($redemptions as $redemption) {
            \App\Models\OtpVerification::factory()->create([
                'redemption_id' => $redemption->id,
            ]);
        }

        // Step 10: Create PMS import logs
        $this->command->info('Creating PMS import logs...');
        \App\Models\PmsImportLog::factory()->count(5)->create();

        $this->command->info('✓ Development database seeded successfully!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info("  - Members: {$members->count()}");
        $this->command->info("  - Outlets: {$outlets->count()}");
        $this->command->info('  - Transactions: '.Transaction::count());
        $this->command->info('  - Redemptions: '.Redemption::count());
        $this->command->info('  - Referrals: '.Referral::count());
        $this->command->info('  - Points Ledger Entries: '.PointsLedger::count());
    }
}
