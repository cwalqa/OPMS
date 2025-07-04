<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseShelf extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'code', 'description', 'is_active'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
