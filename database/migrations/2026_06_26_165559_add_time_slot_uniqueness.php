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
        Schema::table('availabilities', function (Blueprint $table) {
            $table->unique(
                ['doctor_id', 'starts_at', 'ends_at'],
                'availabilities_doctor_start_end_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropUnique('availabilities_doctor_start_end_unique');
        });
    }
};
