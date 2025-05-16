<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackagingLabel extends Model
{
    protected $fillable = [
        'packaging_task_id',
        'label_uuid',
        'qr_image_path',
        'label_data',
        'print_count',
        'last_printed_at',
        'is_primary',
    ];

    protected $casts = [
        'label_data' => 'array',
        'last_printed_at' => 'datetime',
        'is_primary' => 'boolean',
    ];

    public function packagingTask(): BelongsTo
    {
        return $this->belongsTo(PackagingTask::class);
    }

    public static function generateUuid(): string
    {
        return (string) \Str::uuid();
    }
}