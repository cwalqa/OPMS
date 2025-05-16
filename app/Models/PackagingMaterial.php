<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackagingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'dimensions',
        'stock_quantity',
        'reorder_level',
        'cost',
        'supplier_id',
        'last_ordered_at',
        'is_active',
    ];

    protected $casts = [
        'cost' => 'float',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
        'last_ordered_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Material Types Constants
    public const TYPE_BOX = 'box';
    public const TYPE_WRAP = 'wrap';
    public const TYPE_FILLER = 'filler';
    public const TYPE_TAPE = 'tape';
    public const TYPE_LABEL = 'label';
    public const TYPE_OTHER = 'other';

    /**
     * Get all allowed material types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_BOX => 'Box',
            self::TYPE_WRAP => 'Wrap',
            self::TYPE_FILLER => 'Filler',
            self::TYPE_TAPE => 'Tape',
            self::TYPE_LABEL => 'Label',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Check if material is low in stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_level;
    }

    /**
     * Relationship: Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship: Packaging tasks using this material.
     */
    public function packagingTasks(): BelongsToMany
    {
        return $this->belongsToMany(PackagingTask::class, 'packaging_task_materials')
            ->withPivot('quantity_used', 'assigned_by', 'assigned_at')
            ->withTimestamps();
    }
}
