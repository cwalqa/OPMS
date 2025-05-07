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
            $table->string('status')->default('pending');  // Default to 'pending'
            $table->unsignedBigInteger('approved_by')->nullable(); // Stores admin ID for approval
            $table->string('qr_code_path')->nullable();    // Path to QR code

            // Foreign key constraint (assuming you have a QuickbooksAdmin model)
            $table->foreign('approved_by')->references('id')->on('quickbooks_admin')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('quickbooks_estimates', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('approved_by');
            $table->dropColumn('qr_code_path');
        });
    }
};