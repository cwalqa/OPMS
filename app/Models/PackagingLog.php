<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackagingLog extends Model
{
    protected $fillable = [
        'packaging_task_id',
        'action',
        'notes',
        'user_id',
    ];

    public function packagingTask(): BelongsTo
    {
        return $this->belongsTo(PackagingTask::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}