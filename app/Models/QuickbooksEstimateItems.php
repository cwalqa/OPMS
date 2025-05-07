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
        'sku', // Add 'sku' to the fillable attributes
        'amount',  // Add amount which is calculated as quantity * unit_price
        'qr_code_path',  // Add this to allow saving the QR code path
        'tracking_id',  // Add this to allow saving the tracking ID
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

}
