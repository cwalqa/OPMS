<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'production_schedule_id',
        'action',      // e.g., start, pause, resume, complete
        'notes',       // optional notes like defect reason or pause explanation
        'user_id',     // admin who performed the action
    ];

    /**
     * Get the production schedule this log belongs to.
     */
    public function schedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'production_schedule_id');
    }

    /**
     * Get the admin user who performed this action.
     */
    public function user()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'user_id');
    }
}
