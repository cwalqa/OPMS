<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label Sheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .label { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; display: flex; page-break-inside: avoid; }
        .qr { width: 100px; }
        .details { margin-left: 20px; }
    </style>
</head>
<body>
    <h4>Labels for Estimate #{{ $estimate->purchase_order_number }}</h4>

    @foreach($estimate->items as $item)
        @foreach($item->warehouseItems as $wh)
            <div class="label">
                <div class="qr">
                    <img src="{{ public_path('storage/' . $wh->qr_path) }}" width="100">
                </div>
                <div class="details">
                    <div><strong>Product:</strong> {{ $item->product_name }}</div>
                    <div><strong>SKU:</strong> {{ $item->sku }}</div>
                    <div><strong>Tag:</strong> {{ $wh->tag }}</div>
                    <div><strong>Lot/Shelf:</strong> {{ $wh->lot }} / {{ $wh->shelf }}</div>
                </div>
            </div>
        @endforeach
    @endforeach
</body>
</html>
