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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('identifier');
            $table->enum('type', [
                'password_reset',
                'email_verification',
                '2fa',
                'phone_verification',
            ]);

            $table->string('code'); // hashed
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->timestamps();

            $table->index(['identifier', 'type']);
            $table->index(['member_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
