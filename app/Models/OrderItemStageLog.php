<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItemStageLog extends Model
{
    use HasFactory;

    protected $table = 'order_item_stage_logs';

    protected $fillable = [
        'tracking_id',
        'estimate_item_sku',
        'stage',
        'comments',
        'meta',
        'timestamp',
    ];

    protected $casts = [
        'meta' => 'array',
        'timestamp' => 'datetime',
    ];

    /**
     * Get the related estimate item.
     */
    public function estimateItem()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'estimate_item_id');
    }
}
