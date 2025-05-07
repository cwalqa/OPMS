<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory;

    // Define the table if different from the default 'production_batches'
    protected $table = 'production_batches';

    // Fillable properties for mass assignment
    protected $fillable = [
        'batch_number', // Example unique identifier for the batch
        'production_line_id', // ID of the production line associated with this batch
        'status', // Status of the batch (e.g., pending, in production, completed, etc.)
        'quantity', // Quantity of items in this batch
        'start_date', // Start date of production
        'end_date', // End date of production, if applicable
        'defect_details', // Information related to any defects tagged to this batch
    ];

    // Relationships
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }

    public function defects()
    {
        return $this->hasMany(Defect::class, 'batch_id');
    }

    public function items()
    {
        return $this->hasMany(QuickbooksEstimateItems::class, 'batch_id');
    }

    // Additional methods for custom logic can be added here as needed
}
