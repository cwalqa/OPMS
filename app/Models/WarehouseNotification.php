<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseNotification extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'estimate_item_sku',
        'product_name',
        'quantity',
        'production_schedule_id',
        'status',               // pending, assigned, delivered
        'warehouse_location',
        'shelf_number',
        'lot_number',
        'notes',
        'assigned_by',
        'assigned_at',
    ];

    /**
     * Get the associated production schedule.
     */
    public function schedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'production_schedule_id');
    }

    /**
     * Get the admin who assigned the warehouse details.
     */
    public function assignedBy()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'assigned_by');
    }
}
