<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();

            // Correct foreign key columns
            $table->foreignId('estimate_id')->constrained('quickbooks_estimates')->onDelete('cascade');
            $table->foreignId('estimate_item_id')->constrained('quickbooks_estimate_items')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('restrict');

            $table->string('lot');
            $table->string('shelf');
            $table->string('tag')->unique();
            $table->string('qr_path');
            $table->string('sequence_number');
            $table->string('status')->default('in_stock');

            $table->timestamps();

            // Indexes for performance
            $table->index(['estimate_id', 'estimate_item_id']);
            $table->index('tag');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_items');
    }
};
