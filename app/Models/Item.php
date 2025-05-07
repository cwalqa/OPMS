<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'sku', 'brand_id', 'category_id', 'stock', 'sale_price', 'purchase_price', 
        'total_sold_qty', 'total_purchased_qty', 'total_sold', 'total_purchased', 'default_warehouse_id'
    ];

    protected $appends = ['total_expense'];

    // 🔹 Relationship with Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // 🔹 Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔹 Relationship with Transaction History
    public function histories()
    {
        return $this->hasMany(ItemHistory::class);
    }

    // 🔹 Relationship with Default Warehouse
    public function defaultWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }

    // 🔹 🔥 FIX: Relationship with Warehouse Items
    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class, 'item_id');
    }

    // 🔹 Total Expense Calculation (Purchase Price * Stock)
    public function getTotalExpenseAttribute()
    {
        return $this->purchase_price * $this->stock;
    }

    // 🔹 Total Purchased Calculation (Sum of initial stock purchases)
    public function getTotalPurchasedAttribute()
    {
        return $this->histories()->where('action', 'Initial Stock')->sum('amount');
    }
}
