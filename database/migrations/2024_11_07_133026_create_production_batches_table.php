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
    Schema::create('production_batches', function (Blueprint $table) {
        $table->id();
        $table->string('batch_number')->unique();
        $table->foreignId('production_line_id')->constrained()->onDelete('cascade');
        $table->string('status')->default('pending');
        $table->integer('quantity');
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->text('defect_details')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('production_batches');
}
};
