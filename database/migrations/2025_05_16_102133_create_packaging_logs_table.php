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
        Schema::create('packaging_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packaging_task_id');
            $table->string('action');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('packaging_task_id')->references('id')->on('packaging_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('quickbooks_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_logs');
    }
};
