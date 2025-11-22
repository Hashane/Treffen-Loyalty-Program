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
        Schema::table('members', function (Blueprint $table) {
            $table->integer('current_points')->nullable()->default(null)->change();
            $table->integer('lifetime_points')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->integer('current_points')->default(0)->nullable(false)->change();
            $table->integer('lifetime_points')->default(0)->nullable(false)->change();
        });
    }
};
