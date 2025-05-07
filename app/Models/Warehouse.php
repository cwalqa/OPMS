<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'capacity', 'lots'];

    // Automatically cast `lots` as an array
    protected $casts = [
        'lots' => 'array',
    ];


    public function items()
    {
        return $this->belongsToMany(Item::class, 'warehouse_items')
                    ->withPivot('lot_shelf', 'stock')
                    ->withTimestamps();
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }
}
