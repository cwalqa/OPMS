<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWarehouseNotificationsTable extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_notifications', 'warehouse_location_id')) {
                $table->foreignId('warehouse_location_id')->nullable()->after('production_schedule_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_notifications', 'warehouse_location_id')) {
                $table->dropColumn('warehouse_location_id');
            }
        });
    }
}
