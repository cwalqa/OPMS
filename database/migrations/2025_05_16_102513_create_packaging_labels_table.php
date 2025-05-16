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
        Schema::create('packaging_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packaging_task_id');
            $table->string('label_uuid')->unique();
            $table->string('qr_image_path')->nullable();
            $table->json('label_data');
            $table->integer('print_count')->default(0);
            $table->dateTime('last_printed_at')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
            
            $table->foreign('packaging_task_id')->references('id')->on('packaging_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_labels');
    }
};
