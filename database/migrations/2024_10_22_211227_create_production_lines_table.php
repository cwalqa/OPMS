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
        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('line_name');
            $table->integer('max_quantity');
            $table->unsignedBigInteger('line_manager_id'); // Foreign key to QuickbooksAdmin
            $table->unsignedBigInteger('assigned_order_id')->nullable(); // Nullable if no order is assigned
            $table->string('line_status')->default('available');
            $table->integer('current_production')->default(0);
            $table->timestamp('order_deadline')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('line_manager_id')->references('id')->on('quickbooks_admin')->onDelete('cascade');
            $table->foreign('assigned_order_id')->references('id')->on('quickbooks_estimates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_lines');
    }
};
