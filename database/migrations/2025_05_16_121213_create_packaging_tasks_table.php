<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Create packaging tasks table
return new class extends Migration
{
    public function up()
    {
        Schema::create('packaging_tasks', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('warehouse_notification_id');
        $table->unsignedBigInteger('production_schedule_id');
        $table->string('estimate_item_sku');
        $table->string('tracking_id');
        $table->integer('quantity');
        $table->enum('status', ['pending', 'in_progress', 'completed', 'on_hold'])->default('pending');
        $table->unsignedBigInteger('assigned_to')->nullable();
        $table->unsignedBigInteger('assigned_by')->nullable();
        $table->dateTime('assigned_at')->nullable();
        $table->dateTime('started_at')->nullable();
        $table->dateTime('completed_at')->nullable();
        $table->text('packaging_instructions')->nullable();
        $table->boolean('requires_fragile_handling')->default(false);
        $table->boolean('requires_custom_packaging')->default(false);
        $table->boolean('requires_custom_labels')->default(false);
        $table->integer('package_count')->default(1);
        $table->string('package_type')->nullable();
        $table->timestamps();

        $table->foreign('warehouse_notification_id')->references('id')->on('warehouse_notifications')->onDelete('cascade');
        $table->foreign('production_schedule_id')->references('id')->on('production_schedules');
        $table->foreign('assigned_to')->references('id')->on('quickbooks_admin');
        $table->foreign('assigned_by')->references('id')->on('quickbooks_admin');
    });
//     $table->foreign('estimate_item_sku')->references('sku')->on('quickbooks_estimate_items');
    }

    public function down()
    {
        Schema::dropIfExists('packaging_tasks');
    }
};