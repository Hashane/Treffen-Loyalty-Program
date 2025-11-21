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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->decimal('qar_value', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained('reward_categories');
            $table->foreignId('tier_requirement_id')->nullable()->constrained('membership_tiers');
            $table->integer('available_quantity')->nullable();
            $table->boolean('is_unlimited')->default(false);
            $table->string('image_url', 500)->nullable();
            $table->text('terms_conditions')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
