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
            // Add max_buffered_speech field for speech buffering control
            $table->integer('max_buffered_speech')
                  ->default(60)
                  ->after('prefix_padding_duration')
                  ->comment('Maximum amount of speech data to buffer before processing (40 to 80 seconds, 1 second precision)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_buffered_speech');
        });
    }
};
