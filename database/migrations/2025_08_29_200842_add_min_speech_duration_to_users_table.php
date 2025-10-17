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
            // Add min_speech_duration field for speech processing control
            $table->decimal('min_speech_duration', 3, 2)
                  ->default(0.05)
                  ->after('max_endpointing_delay')
                  ->comment('Minimum duration of speech before it is considered valid (0.02 to 0.35 seconds, 0.01 precision)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('min_speech_duration');
        });
    }
};
