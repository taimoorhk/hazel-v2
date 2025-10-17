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
            // Add max_endpointing_delay field for audio processing endpointing control
            $table->decimal('max_endpointing_delay', 3, 1)
                  ->default(6.0)
                  ->after('min_endpointing_delay')
                  ->comment('Maximum delay before endpointing audio processing (3.0 to 9.0 seconds, 0.5 divisions)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_endpointing_delay');
        });
    }
};
