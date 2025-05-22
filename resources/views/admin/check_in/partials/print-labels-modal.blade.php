<div class="modal-header bg-gradient-info text-white">
    <h5 class="modal-title">Print Labels – PO #{{ $estimate->purchase_order_number }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    @php
        $warehouseItems = $estimate->items->flatMap->warehouseItems;
    @endphp

    @if($warehouseItems->isEmpty())
        <div class="alert alert-warning">No checked-in items available for printing.</div>
    @else
        <div class="row">
            @foreach ($warehouseItems as $item)
                <div class="col-md-3 mb-4 text-center">
                    <img src="{{ asset('storage/' . $item->qr_path) }}" alt="QR" class="img-thumbnail">
                    <div class="mt-2 small fw-bold">{{ $item->tag }}</div>
                    <div class="small text-muted">{{ $item->estimateItem->product_name ?? '—' }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="modal-footer">
    <a href="{{ route('admin.check_in.generate_pdf', $estimate->id) }}" class="btn btn-outline-info" target="_blank">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
    </a>
</div>
