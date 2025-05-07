<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('quickbooks_admin', function (Blueprint $table) {
            $table->id(); // Primary key for admin
            $table->string('name'); // Admin name
            $table->string('email')->unique(); // Admin email
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // Admin password
            $table->string('remember_token')->nullable(); // Token for "Remember Me" functionality
            $table->string('two_factor_code')->nullable(); // 2FA code
            $table->timestamp('two_factor_expires_at')->nullable(); // Expiration of 2FA code
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quickbooks_admin');
    }
};