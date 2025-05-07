<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class ItemHistory extends Model
{
   

    protected $fillable = ['item_id', 'action', 'quantity', 'amount', 'note'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
{
    return $this->belongsTo(Warehouse::class, 'warehouse_id');
}

}