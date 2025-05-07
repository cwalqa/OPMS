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
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            // Add amount field after quantity
            $table->decimal('amount', 10, 2)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            // Remove amount field
            $table->dropColumn('amount');
        });
    }
};
