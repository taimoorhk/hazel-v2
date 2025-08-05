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
        Schema::table('elderly_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('elderly_profiles', 'name')) {
                $table->string('name')->after('id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elderly_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('elderly_profiles', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
