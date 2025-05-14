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
        Schema::create('delivery_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_notification_id')->constrained();
            $table->dateTime('delivery_date');
            $table->string('delivery_time_window')->nullable();
            $table->text('destination_address');
            $table->string('recipient_name');
            $table->string('recipient_contact');
            $table->foreignId('delivery_agent_id')->nullable();
            $table->enum('status', ['scheduled', 'in_transit', 'delivered', 'failed', 'rescheduled'])->default('scheduled');
            $table->text('delivery_notes')->nullable();
            $table->text('special_instructions')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->string('proof_of_delivery')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_schedules');
    }
};
