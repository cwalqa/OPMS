<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'description',
        'quantity',
        'defect_type',
        'severity',
        'status',
        'notes',
    ];

    public function item()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'item_id');
    }
}
