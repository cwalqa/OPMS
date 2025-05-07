<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_customer', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->length(11)->nullable(false);
            $table->integer('customer_id')->length(11)->unsigned()->nullable()->unique();
            $table->string('fully_qualified_name', 100)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('display_name', 200)->nullable();
            $table->enum('is_active',['0','1'])->default('0');
            $table->string('email', 255)->nullable();
            $table->string('password', 255);
            $table->timestamp('password_changed_at')->nullable(); // To track if the password has been changed
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable(true)->useCurrent()->useCurrentOnUpdate();

        });

        Schema::create('quickbooks_item', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->length(11)->nullable(false);
            $table->integer('item_id')->length(11)->unsigned()->nullable()->unique();
            $table->string('fully_qualified_name', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('item_description', 200)->nullable();
            $table->integer('qty_on_hand')->length(11)->unsigned()->nullable();
            $table->decimal('unit_price', 10, 2)->nullable(); // If available
            $table->string('income_account_ref', 100)->nullable(); // If available
            $table->enum('is_active',['0','1'])->default('0');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable(true)->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quickbooks_customer');
        Schema::dropIfExists('quickbooks_item');
    }
};
