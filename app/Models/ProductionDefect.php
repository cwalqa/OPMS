<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDefect extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_schedule_id',
        'estimate_item_sku',
        'tracking_id',
        'defect_type',
        'severity',
        'quantity',
        'description',
        'status',
        'reported_by',
        'corrective_action',
        'action_taken_by',
        'action_taken_at',
        'root_cause',
    ];

    protected $casts = [
        'action_taken_at' => 'datetime',
        'quantity' => 'integer',
    ];

    // Severity constants
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    // Status constants
    const STATUS_REPORTED = 'reported';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REWORK = 'rework';
    const STATUS_DISCARD = 'discard';
    const STATUS_RESOLVED = 'resolved';

    // Defect type constants
    const TYPE_MATERIAL = 'material';
    const TYPE_ASSEMBLY = 'assembly';
    const TYPE_FINISHING = 'finishing';
    const TYPE_PACKAGING = 'packaging';
    const TYPE_OTHER = 'other';
    
    // Relationships
    public function productionSchedule()
    {
        return $this->belongsTo(ProductionSchedule::class);
    }
    
    public function estimateItem()
    {
        return $this->belongsTo(QuickbooksEstimateItems::class, 'estimate_item_sku', 'sku');
    }
    
    public function reporter()
    {
        return $this->belongsTo(\App\Models\QuickbooksAdmin::class, 'reported_by');
    }
    
    public function actionTaker()
    {
        return $this->belongsTo(\App\Models\QuickbooksAdmin::class, 'action_taken_by');
    }
    
    // Get the available defect types
    public static function getDefectTypes()
    {
        return [
            self::TYPE_MATERIAL => 'Material Defect',
            self::TYPE_ASSEMBLY => 'Assembly Defect',
            self::TYPE_FINISHING => 'Finishing Defect',
            self::TYPE_PACKAGING => 'Packaging Defect',
            self::TYPE_OTHER => 'Other',
        ];
    }
    
    // Get the available severity levels
    public static function getSeverityLevels()
    {
        return [
            self::SEVERITY_LOW => 'Low',
            self::SEVERITY_MEDIUM => 'Medium',
            self::SEVERITY_HIGH => 'High',
            self::SEVERITY_CRITICAL => 'Critical',
        ];
    }
    
    // Get the available statuses
    public static function getStatuses()
    {
        return [
            self::STATUS_REPORTED => 'Reported',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_REWORK => 'Scheduled for Rework',
            self::STATUS_DISCARD => 'Marked for Discard',
            self::STATUS_RESOLVED => 'Resolved',
        ];
    }
}