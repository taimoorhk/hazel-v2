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
            if (!Schema::hasColumn('elderly_profiles', 'associated_account_email')) {
                $table->string('associated_account_email')->after('temporary_role');
            }
            if (!Schema::hasIndex('elderly_profiles', 'elderly_profiles_associated_account_email_index')) {
                $table->index('associated_account_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elderly_profiles', function (Blueprint $table) {
            if (Schema::hasIndex('elderly_profiles', 'elderly_profiles_associated_account_email_index')) {
                $table->dropIndex(['associated_account_email']);
            }
            if (Schema::hasColumn('elderly_profiles', 'associated_account_email')) {
                $table->dropColumn('associated_account_email');
            }
        });
    }
};
