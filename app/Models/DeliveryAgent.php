<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'vehicle_type', 
        'vehicle_registration',
        'max_load_capacity', 
        'is_active', 
        'notes'
    ];
}
