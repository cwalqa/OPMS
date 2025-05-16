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
        Schema::create('packaging_task_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packaging_task_id');
            $table->unsignedBigInteger('packaging_material_id');
            $table->integer('quantity_used')->default(1);
            $table->timestamps();
            
            $table->foreign('packaging_task_id')->references('id')->on('packaging_tasks')->onDelete('cascade');
            $table->foreign('packaging_material_id')->references('id')->on('packaging_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_task_materials');
    }
};
