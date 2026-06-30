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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('status');
            $table->string('cancel_reason')->nullable();

            $table->foreignId('doctor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
