<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'item_id',
        'quantity',
        'status',
        'delivery_date',
        'notes',
        'assigned_dispatch',
        'delivery_note',
    ];

    public function item()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'item_id');
    }

    public function assignedDispatch()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'assigned_dispatch');
    }
}
