<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_code', 
        'zone', 
        'aisle', 
        'rack', 
        'shelf', 
        'description', 
        'is_active'
    ];
}