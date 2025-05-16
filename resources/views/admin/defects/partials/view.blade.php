@php
    $item = \App\Models\QuickbooksEstimateItems::where('sku', $defect->item_sku)->with('order')->first();
    $product = \App\Models\QuickbooksItem::where('sku', $defect->item_sku)->first();
@endphp

<div class="modal fade" id="viewModal{{ $defect->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Defect Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="alert alert-light">
                            <p><strong>Order No:</strong> {{ $defect->estimateItem?->order?->purchase_order_number ?? '-' }}</p>
                            <p><strong>Item SKU:</strong> {{ $defect->estimate_item_sku }}</p>
                            <p><strong>Product:</strong> {{ $defect->estimateItem?->name ?? '-' }}</p>
                            <p><strong>Quantity:</strong> {{ $defect->quantity }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-light">
                            <p><strong>Type:</strong> {{ ucfirst($defect->defect_type) }}</p>
                            <p><strong>Severity:</strong> {{ ucfirst($defect->severity) }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($defect->status) }}</p>
                            <p><strong>Reported On:</strong> {{ $defect->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-secondary">
                            <p class="mb-1"><strong>Description:</strong></p>
                            <p>{{ $defect->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
