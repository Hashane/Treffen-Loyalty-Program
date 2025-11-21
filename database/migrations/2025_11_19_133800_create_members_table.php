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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_number', 20);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('qatar_id_or_passport', 50);
            $table->string('id_type');
            $table->date('date_of_birth')->nullable();
            $table->string('email', 255);
            $table->string('phone', 20);
            $table->string('preferred_communication')->default('EMAIL');
            $table->string('password', 255);
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token', 255)->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('password_reset_token', 255)->nullable();
            $table->timestamp('password_reset_expires')->nullable();
            $table->string('qr_code_path', 500)->nullable();
            $table->text('qr_code_data')->nullable();
            $table->foreignId('membership_tier_id')->constrained('membership_tiers');
            $table->integer('current_points')->default(100);
            $table->integer('lifetime_points')->default(100);
            $table->string('referral_code', 20);
            $table->foreignId('referred_by_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('status')->default('ACTIVE');
            $table->timestamp('enrolled_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
