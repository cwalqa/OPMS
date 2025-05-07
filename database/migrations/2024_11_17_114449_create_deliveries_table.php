<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // In your deliveries migration
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->foreignId('item_id')->constrained('quickbooks_estimate_items');
            $table->integer('quantity');
            $table->string('status');
            $table->date('delivery_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_dispatch')->nullable();
            $table->foreign('assigned_dispatch')->references('id')->on('quickbooks_admins')->onDelete('set null');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
