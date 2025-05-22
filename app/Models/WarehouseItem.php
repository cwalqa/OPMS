<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'estimate_item_id',
        'warehouse_id',
        'lot',
        'shelf',
        'tag',
        'qr_path',
        'sequence_number',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to QuickbooksEstimate
     */
    public function estimate()
    {
        return $this->belongsTo(QuickbooksEstimates::class, 'estimate_id');
    }

    /**
     * Relationship to QuickbooksEstimateItem
     */
    public function estimateItem()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'estimate_item_id');
    }

    /**
     * Warehouse relationship
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Human-readable location string
     */
    public function getLocationAttribute()
    {
        return "{$this->warehouse->name} / Lot {$this->lot} / Shelf {$this->shelf}";
    }

    /**
     * QR Code URL accessor
     */
    public function getQrUrlAttribute()
    {
        return asset('storage/' . $this->qr_path);
    }
}
