<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_schedule_id',
         'action', 
         'notes',
        'latitude', 
        'longitude', 
        'user_id'
    ];
}
