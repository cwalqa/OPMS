<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quickbooks_estimates', function (Blueprint $table) {
            $table->id();

            // QuickBooks Specific Identifiers
            $table->string('qb_estimate_id')->default(null)->nullable(); // Unique QuickBooks ID

            // Customer Information
            $table->string('customer_ref')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_memo')->nullable();
            $table->string('bill_email')->nullable(); // For emailing estimates

            $table->enum('is_updated', ['0', '1'])->default('0');
            $table->timestamps();
            $table->timestamp('synced_at')->nullable();
        });

        Schema::create('quickbooks_estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quickbooks_estimate_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('sku')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable(); // If available
            $table->decimal('quantity', 10, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quickbooks_estimate_items'); 
        Schema::dropIfExists('quickbooks_estimates');
    }
};
