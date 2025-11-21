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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('check_number', 100);
            $table->string('guest_name', 200)->nullable();
            $table->string('department', 50)->nullable();
            $table->timestamp('transaction_date');
            $table->string('booking_reference', 100)->nullable();
            $table->string('hotel_property', 100)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->integer('points_earned')->default(0);
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
