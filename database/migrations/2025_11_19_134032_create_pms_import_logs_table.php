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
        Schema::create('pms_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('import_type')->nullable();
            $table->string('file_name', 255)->nullable();
            $table->integer('file_size_kb')->nullable();
            $table->integer('records_processed')->default(0);
            $table->integer('records_successful')->default(0);
            $table->integer('records_failed')->default(0);
            $table->integer('records_duplicate')->default(0);
            $table->json('error_details')->nullable();
            $table->json('summary')->nullable();
            $table->string('status')->default('PROCESSING');
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pms_import_logs');
    }
};
