<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuickbooksEstimateItems;
use App\Models\QuickbooksAdmin;

class QuickbooksEstimates extends Model
{
    use HasFactory;

    protected $casts = [
        'po_date' => 'date',
    ];

    protected $fillable = [
        'qb_estimate_id',
        'customer_ref',
        'customer_name',
        'customer_memo',
        'bill_email',
        'total_amount',
        'discount_amount',
        'apply_tax_after_discount',
        'print_status',
        'email_status',
        'synced_at',
        'purchase_order_number',
        'status',               // Add status field (pending, approved, declined)
        'approved_by',           // Admin who approved the order
        'qr_code_path',          // Path to QR code for the order
        'decline_reason',        // Reason for declining the order
        'cancel_reason',         // Reason for cancelling the order
        'client_po_number',     // NEW: Client-provided PO number
        'po_document_path',     // NEW: Path to uploaded PO document
        'description',          // NEW: Free-text description of the estimate
        'po_date',
    ];

    // Relationship with the items in the order
    public function items()
    {
        return $this->hasMany(QuickbooksEstimateItems::class, 'quickbooks_estimate_id', 'id');
    }

    // Relationship with the admin who approved the order
    public function approvedBy()
    {
        return $this->belongsTo(QuickbooksAdmin::class, 'approved_by');
    }

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(QuickbooksCustomer::class, 'customer_ref', 'customer_id');
    }

    
}
