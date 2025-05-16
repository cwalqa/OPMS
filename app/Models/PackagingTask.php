<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackagingTask extends Model
{
    protected $fillable = [
        'warehouse_notification_id',
        'tracking_id',
        'estimate_item_sku',
        'quantity',
        'packaging_type',
        'packaging_notes',
        'special_instructions',
        'priority',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ON_HOLD = 'on_hold';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ON_HOLD => 'On Hold',
        ];
    }

    // Relationships

    /**
     * Get the associated warehouse notification.
     */
    public function warehouseNotification(): BelongsTo
    {
        return $this->belongsTo(WarehouseNotification::class, 'warehouse_notification_id');
    }

    /**
     * Get the estimate item (optional).
     */
    public function estimateItem(): BelongsTo
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'estimate_item_sku', 'sku');
    }

    /**
     * Creator admin.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'created_by');
    }

    /**
     * Task logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PackagingLog::class);
    }

    /**
     * Materials used for packaging task.
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(PackagingMaterial::class, 'packaging_task_materials')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }

    /**
     * Labels generated for the task.
     */
    public function labels(): HasMany
    {
        return $this->hasMany(PackagingLabel::class);
    }

    public function getPrimaryLabelAttribute()
    {
        return $this->labels()->where('is_primary', true)->first();
    }
}
