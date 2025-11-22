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
            $table->string('qatar_id_or_passport')->nullable()->change();
            $table->string('id_type')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('preferred_communication')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('qatar_id_or_passport')->nullable(false)->change();
            $table->string('id_type')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('preferred_communication')->nullable(false)->change();
        });
    }
};
