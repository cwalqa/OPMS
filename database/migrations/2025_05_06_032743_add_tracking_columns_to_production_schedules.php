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
        Schema::table('production_schedules', function (Blueprint $table) {
            //
            $table->timestamp('start_date')->nullable();
            $table->timestamp('last_paused_at')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->integer('defective_quantity')->nullable()->after('quantity');
            $table->text('defect_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_schedules', function (Blueprint $table) {
            //
        });
    }
};
