<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\WarehouseShelf;
use App\Models\WarehouseLot;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Warehouse → Warehouse Items relationship
     */
    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    /**
     * Warehouse → Lots relationship
     */
    public function lots()
    {
        return $this->hasMany(WarehouseLot::class);
    }

    /**
     * Count of in-stock items
     */
    public function getItemsCountAttribute()
    {
        return $this->warehouseItems()
            ->where('status', 'in_stock')
            ->count();
    }

    /**
     * Unique SKUs in warehouse (from joined order_items)
     */
    public function getUniqueSkusCountAttribute()
    {
        return $this->warehouseItems()
            ->join('order_items', 'warehouse_items.order_item_id', '=', 'order_items.id')
            ->where('warehouse_items.status', 'in_stock')
            ->distinct('order_items.sku')
            ->count('order_items.sku');
    }

    /**
     * Scope for active warehouses only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function shelves()
{
    return $this->hasMany(WarehouseShelf::class);
}

}
