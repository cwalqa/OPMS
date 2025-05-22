<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseShelvesTable extends Migration
{
    public function up()
    {
        Schema::create('warehouse_shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('warehouse_lot_id');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_shelves');
    }
}
