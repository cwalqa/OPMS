<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('sku');
        });
    }

    public function down()
    {
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
};
