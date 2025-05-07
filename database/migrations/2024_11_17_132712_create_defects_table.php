<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefectsTable extends Migration
{
    public function up()
    {
        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('quickbooks_estimate_items');
            $table->string('description');
            $table->integer('quantity');
            $table->string('defect_type');
            $table->string('severity');
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('defects');
    }
}
