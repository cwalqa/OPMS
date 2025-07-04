<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseLotsTable extends Migration
{
    public function up()
    {
        Schema::create('warehouse_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('warehouse_id');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_lots');
    }
}
