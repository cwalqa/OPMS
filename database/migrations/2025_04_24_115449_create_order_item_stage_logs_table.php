<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemStageLogsTable extends Migration
{
    public function up()
{
    Schema::create('order_item_stage_logs', function (Blueprint $table) {
        $table->id();

        $table->uuid('tracking_id');
        $table->foreignId('sku')
              ->constrained('quickbooks_estimate_items') // this ensures correct type and FK
              ->onDelete('cascade');

        $table->string('stage');
        $table->text('comments')->nullable();
        $table->json('meta')->nullable();
        $table->timestamp('timestamp')->useCurrent();

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('order_item_stage_logs');
    }
};

