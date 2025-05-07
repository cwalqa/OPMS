<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'source_warehouse_id',
        'destination_warehouse_id',
        'source_lot_shelf',
        'destination_lot_shelf',
        'quantity',
        'notes',
        'user_id'
    ];

    /**
     * Get the item associated with the transfer.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the source warehouse.
     */
    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    /**
     * Get the destination warehouse.
     */
    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    /**
     * Get the user who executed the transfer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}