<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLotAndShelfNullableInWarehouseItemsTable extends Migration
{
    public function up()
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->string('lot')->nullable()->change();
            $table->string('shelf')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->string('lot')->nullable(false)->change();
            $table->string('shelf')->nullable(false)->change();
        });
    }
}
