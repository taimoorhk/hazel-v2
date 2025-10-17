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
            $table->string('preferred_voice')->default('sage')->after('associated_account_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elderly_profiles', function (Blueprint $table) {
            $table->dropColumn('preferred_voice');
        });
    }
};
