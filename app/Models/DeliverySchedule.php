<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_notification_id', 
        'delivery_date', 
        'delivery_time_window',
        'destination_address', 
        'recipient_name', 
        'recipient_contact',
        'delivery_agent_id', 
        'status', 
        'delivery_notes', 
        'special_instructions',
        'completed_at', 
        'completion_notes', 
        'proof_of_delivery'
    ];
}
