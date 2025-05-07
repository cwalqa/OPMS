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
        Schema::create('admin_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); // Foreign key for QuickbooksAdmin
            $table->unsignedBigInteger('role_id');  // Foreign key for Role
            $table->foreign('admin_id')->references('id')->on('quickbooks_admin')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role');
    }
};
