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
            // Add activation_threshold field for speech processing activation control
            $table->decimal('activation_threshold', 2, 2)
                  ->default(0.5)
                  ->after('max_buffered_speech')
                  ->comment('Threshold for activating speech processing (0.1 to 0.9, 0.01 precision)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_threshold');
        });
    }
};
