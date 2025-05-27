<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuickbooksEstimates;

class QuickbooksEstimateItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'quickbooks_estimate_id',
        'line_num',
        'description',
        'unit_price',
        'quantity',
        'item_ref',
        'tax_code_ref',
        'detail_type',
        'discount_percent',
        'sku',
        'amount',
        'qr_code_path',
        'tracking_id',
        'check_in_status',
        'product_name', // ✅ Add this line
    ];

    protected $attributes = [
        'check_in_status' => 'pending',
    ];

    public function estimate()
    {
        return $this->belongsTo(QuickbooksEstimates::class);
    }

    public function order()
    {
        return $this->belongsTo(QuickbooksEstimates::class, 'quickbooks_estimate_id');
    }

    public function productionSchedule()
    {
        return $this->hasOne(ProductionSchedule::class, 'item_id');
    }

    public function productionSchedules()
    {
        return $this->hasMany(ProductionSchedule::class, 'item_id');
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class, 'estimate_item_id');
    }

}
