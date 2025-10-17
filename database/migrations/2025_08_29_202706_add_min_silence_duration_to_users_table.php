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
            // Add min_silence_duration field for silence processing control
            $table->decimal('min_silence_duration', 3, 2)
                  ->default(0.55)
                  ->after('min_speech_duration')
                  ->comment('Minimum duration of silence before it is considered a pause/break (0.25 to 1.4 seconds, 0.01 precision)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('min_silence_duration');
        });
    }
};
