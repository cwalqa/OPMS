<div class="modal-header bg-gradient-primary text-white">
    <h5 class="modal-title">Preview Labels – PO #{{ $estimate->purchase_order_number }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    @forelse ($previewItems as $group)
        <div class="card p-3 mb-3 shadow-sm border">
            <h6 class="fw-bold">{{ $group['item']->product_name }} (SKU: {{ $group['item']->sku }})</h6>
            <div class="d-flex gap-3 flex-wrap">
                @foreach ($group['qr_previews'] as $qr)
                    <div class="text-center border p-2 rounded">
                        {!! $qr['qr_svg'] !!}
                        <div class="small mt-1">{{ $qr['tag'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-muted">No items to preview.</p>
    @endforelse

    @if(count($skippedItems))
        <div class="alert alert-warning mt-3">
            Some items were skipped as they are already fully checked in.
        </div>
    @endif
</div>

<div class="modal-footer">
    <form method="POST" action="{{ route('admin.check_in.process', $estimate->id) }}">
        @csrf
        {{-- Since the preview modal doesn't carry full originalRequest data, submit dummy fields or modify backend to re-fetch --}}
        <input type="hidden" name="confirm_checkin" value="1">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-check-circle me-1"></i> Confirm & Check-In
        </button>
    </form>
</div>
