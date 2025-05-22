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
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            //
            $table->string('check_in_status')->default('pending')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quickbooks_estimate_items', function (Blueprint $table) {
            //
            $table->dropColumn('check_in_status');
        });
    }
};
