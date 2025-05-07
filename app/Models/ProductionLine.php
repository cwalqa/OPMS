<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_name', 
        'max_quantity', 
        'line_manager_id', 
        'assigned_order_id', 
        'line_status',
        'current_production',
        'order_deadline'
    ];

    // Relationship to the line manager (admin)
    public function lineManager()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'line_manager_id');
    }

    // Relationship to the assigned order
    public function assignedOrder()
    {
        return $this->belongsTo(QuickbooksEstimates::class, 'assigned_order_id');
    }
}
