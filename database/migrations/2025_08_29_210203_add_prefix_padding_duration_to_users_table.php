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
            // Add prefix_padding_duration field for speech processing control
            $table->decimal('prefix_padding_duration', 3, 2)
                  ->default(0.5)
                  ->after('min_silence_duration')
                  ->comment('Duration of padding added before speech processing (0.2 to 1.2 seconds, 0.01 precision)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('prefix_padding_duration');
        });
    }
};
