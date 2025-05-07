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
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
            //
            // Add purchase_order_number after bill_email
            $table->string('purchase_order_number')->after('bill_email');
            // Add total_amount after purchase_order_number
            $table->decimal('total_amount', 10, 2)->after('purchase_order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
             // Remove purchase_order_number and total_amount fields
             $table->dropColumn('purchase_order_number');
             $table->dropColumn('total_amount');
        });
    }
};
