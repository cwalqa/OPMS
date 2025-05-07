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
            $table->string('schedule_status')
                  ->default('scheduled')
                  ->after('deadline_date');
        });
    }

    public function down(): void
    {
        Schema::table('production_schedules', function (Blueprint $table) {
            $table->dropColumn('schedule_status');
        });
    }
};
