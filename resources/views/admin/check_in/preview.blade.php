@extends('admin.app')

@section('content')
<div class="container py-4">
    <h4>Preview Labels – PO #{{ $estimate->purchase_order_number }}</h4>

    @foreach ($previewItems as $group)
        <div class="card p-3 mb-3">
            <h6>{{ $group['item']->product_name }} ({{ $group['item']->sku }})</h6>
            <div class="d-flex gap-2 flex-wrap">
                @foreach ($group['qr_previews'] as $qr)
                    <div class="text-center">
                        {!! $qr['qr_svg'] !!}
                        <div class="small mt-1">{{ $qr['tag'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(count($skippedItems))
        <div class="alert alert-warning">
            Some items were skipped as they are already fully checked in.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.check_in.process', $estimate->id) }}">
        @csrf
        @foreach ($previewItems as $group)
            @php
                $itemId = $group['item']->id;
                $data = $group['location'];
            @endphp
            <input type="hidden" name="items[{{ $itemId }}][warehouse_id]" value="{{ $data['warehouse_id'] }}">
            <input type="hidden" name="items[{{ $itemId }}][lot]" value="{{ $data['lot'] ?? '' }}">
            <input type="hidden" name="items[{{ $itemId }}][shelf]" value="{{ $data['shelf'] ?? '' }}">
        @endforeach
        <button type="submit" class="btn btn-success mt-3">
            ✔ Confirm & Check-In
        </button>
    </form>
</div>
@endsection
