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
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
            $table->dateTime('po_date')->nullable()->after('client_po_number');
        });
    }

    public function down()
    {
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
            $table->dropColumn('po_date');
        });
    }
};
