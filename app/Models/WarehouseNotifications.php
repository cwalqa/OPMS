<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseNotifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'estimate_item_sku',
        'product_name',
        'quantity',
        'production_schedule_id',
        'status',
        'warehouse_location',
        'shelf_number',
        'lot_number',
        'notes',
        'assigned_by',
        'assigned_at',
    ];

    public function schedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'production_schedule_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'assigned_by');
    }
}
