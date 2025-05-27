@extends('admin.app')

@section('content')
<div class="watermark">DECLINED ORDER</div>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Purchase Order Details: #{{ $order->purchase_order_number }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <!-- Order Details -->
                    <div class="table-responsive p-3">
                        <div class="row d-flex justify-content-center align-items-center mb-3 order-info-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_name"><b>Company Name</b></label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $order->customer->company_name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="order_date"><b>Order Date</b></label>
                                    <input type="text" name="order_date" id="order_date" class="form-control" value="{{ $order->created_at->format('Y-m-d') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_amount"><b>Total Amount</b></label>
                                    <input type="text" name="total_amount" id="total_amount" class="form-control" value="${{ number_format($order->total_amount, 2) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <!-- List of Order Items -->
                        <h6 class="text-center mt-5 mb-3"><b>ORDER ITEMS</b></h6>
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Product Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Product ID</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Quantity</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Unit Price</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                    $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();
                                    $productName = $product ? $product->name : 'Unknown Product';
                                @endphp
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $productName }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $item->sku }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $item->quantity }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">${{ number_format($item->unit_price, 2) }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">${{ number_format($item->amount, 2) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr />

                        <!-- Print Order Button -->
                        <!-- Back + Print Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                            <!-- Back Button (left) -->
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>

                            <!-- Print Button (right) -->
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> Print Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .watermark {
        visibility: hidden; /* Hides the text but keeps the element visible for CSS rules */
    }
    @media print {
        .order-info-row {
            display: flex;
            justify-content: space-between; /* Ensure the columns are properly spaced */
        }

        .order-info-row .col-md-3 {
            float: left;
            width: 23%; /* Adjust the width to fit all columns on a single row */
        }
        
        /* Ensure no page breaks within the order table */
        .table-responsive {
            page-break-inside: avoid;
        }

        /* Watermark Styling */
        .watermark {
            visibility: visible; /* Makes the text visible in print */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg); /* Rotate the text diagonally */
            font-size: 5rem;
            color: rgba(255, 0, 0, 0.2); /* Light red watermark */
            z-index: 9999; /* Ensure it is above all other elements */
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
            font-weight: bold;
        }
    }
</style>

@endsection
