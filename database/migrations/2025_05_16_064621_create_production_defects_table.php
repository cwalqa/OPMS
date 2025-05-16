<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionDefectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_defects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_schedule_id');
            $table->string('estimate_item_sku');
            $table->uuid('tracking_id');
            $table->string('defect_type');
            $table->string('severity');
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->string('status')->default('reported');
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->text('corrective_action')->nullable();
            $table->unsignedBigInteger('action_taken_by')->nullable();
            $table->timestamp('action_taken_at')->nullable();
            $table->text('root_cause')->nullable();
            $table->timestamps();

            // Foreign keys (optional but recommended if enforcing relations)
            $table->foreign('production_schedule_id')->references('id')->on('production_schedules')->onDelete('cascade');
            $table->foreign('estimate_item_sku')->references('sku')->on('quickbooks_estimate_items')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('quickbooks_admin')->onDelete('set null');
            $table->foreign('action_taken_by')->references('id')->on('quickbooks_admin')->onDelete('set null');    

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_defects');
    }
}
