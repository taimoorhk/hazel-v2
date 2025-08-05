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
        Schema::create('elderly_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('temporary_role', 50)->default('elderly user');
            $table->string('associated_account_email');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('email');
            $table->index('phone');
            $table->index('associated_account_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elderly_profiles');
    }
};
