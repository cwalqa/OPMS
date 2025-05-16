<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionDefect;

class ProductionSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'line_id',
        'quantity',
        'schedule_date',
        'deadline_date',
        'schedule_status',
        'start_date',
        'last_paused_at',
        'completion_date',
        'defective_quantity',
        'defect_notes',
    ];

    public function item()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'item_id');
    }

    public function line()
    {
        return $this->belongsTo(ProductionLine::class, 'line_id');
    }

    public function logs()
    {
        return $this->hasMany(ProductionLog::class, 'production_schedule_id');
    }

    public function defects()
    {
        return $this->hasMany(ProductionDefect::class, 'production_schedule_id', 'id');
    }
}
