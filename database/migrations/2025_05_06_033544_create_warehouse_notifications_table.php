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
        Schema::create('warehouse_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id');
            $table->string('estimate_item_sku');
            $table->string('product_name');
            $table->integer('quantity');
            $table->unsignedBigInteger('production_schedule_id');
            $table->string('status')->default('pending'); // pending, assigned, delivered
            $table->string('warehouse_location')->nullable();
            $table->string('shelf_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            
            $table->foreign('production_schedule_id')
                  ->references('id')
                  ->on('production_schedules')
                  ->onDelete('cascade');
                  
            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('quickbooks_admin')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_notifications');
    }
};
