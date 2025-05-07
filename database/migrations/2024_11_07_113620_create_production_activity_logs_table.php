<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('production_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('quickbooks_estimate_items')->onDelete('cascade');
            $table->foreignId('production_line_id')->constrained()->onDelete('cascade');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // e.g., 'in-progress', 'completed'
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('defect_details')->nullable(); // Optional defect notes
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_activity_logs');
    }
};