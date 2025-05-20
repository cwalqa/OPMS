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
            $table->string('client_po_number')->nullable()->after('purchase_order_number'); // replace with actual last column name
            $table->string('po_document_path')->nullable()->after('client_po_number');
            $table->text('description')->nullable()->after('po_document_path');
        });
    }

    public function down()
    {
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
            $table->dropColumn(['client_po_number', 'po_document_path', 'description']);
        });
    }
};



