<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('referred_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('referral_code', 20);
            $table->string('referred_email', 255)->nullable();
            $table->string('referred_phone', 20)->nullable();
            $table->integer('bonus_points_awarded')->default(20);

            $table->foreignId('points_ledger_id')->nullable()->constrained('points_ledgers')->nullOnDelete();
            $table->string('status')->default('PENDING');
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
