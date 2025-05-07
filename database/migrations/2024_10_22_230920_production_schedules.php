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
        //
        Schema::create('production_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('line_id');
            $table->integer('quantity');
            $table->date('schedule_date');
            $table->date('deadline_date');
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('quickbooks_estimate_items')->onDelete('cascade');
            $table->foreign('line_id')->references('id')->on('production_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
