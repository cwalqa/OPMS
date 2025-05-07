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
        Schema::table('production_activity_logs', function (Blueprint $table) {
            $table->string('purchase_order_number')->after('item_id'); // Adjust the position as needed
        });
    }

    public function down()
    {
        Schema::table('production_activity_logs', function (Blueprint $table) {
            $table->dropColumn('purchase_order_number');
        });
    }
};
