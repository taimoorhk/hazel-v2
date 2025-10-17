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
        Schema::table('users', function (Blueprint $table) {
            // Add min_endpointing_delay field for audio processing endpointing control
            $table->decimal('min_endpointing_delay', 3, 1)
                  ->default(0.5)
                  ->after('user_pronouns')
                  ->comment('Minimum delay before endpointing audio processing (0.0 to 2.0 seconds, 0.5 divisions)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('min_endpointing_delay');
        });
    }
};
