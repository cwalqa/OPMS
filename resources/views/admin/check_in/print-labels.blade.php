@extends('admin.app')

@section('content')
<div class="container py-4">
    <h4>Print Labels – PO #{{ $estimate->purchase_order_number }}</h4>

    @php
        $warehouseItems = $estimate->items->flatMap->warehouseItems;
    @endphp

    @if ($warehouseItems->isEmpty())
        <div class="alert alert-warning">No warehouse items found for printing.</div>
    @else
        <div class="row">
            @foreach ($warehouseItems as $item)
                <div class="col-md-3 mb-4 text-center">
                    <img src="{{ asset('storage/' . $item->qr_path) }}" class="img-fluid border" alt="QR Code">
                    <div class="fw-bold small mt-2">{{ $item->tag }}</div>
                    <div class="text-muted small">{{ $item->estimateItem->product_name ?? '—' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.check_in.generate_pdf', $estimate->id) }}" class="btn btn-outline-primary" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>
@endsection
