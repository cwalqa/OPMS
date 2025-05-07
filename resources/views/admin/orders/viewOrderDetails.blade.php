@extends('admin.app')

@section('content')
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
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">QR Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                    // Lookup product name based on SKU
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
                                    <td>
                                        <!-- Download QR Code for this item -->
                                        <a href="{{ asset($item->qr_code_path) }}" class="btn btn-info btn-sm" download>Download QR</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr />

                        <!-- Approve or Decline Order -->
                        <div class="d-flex justify-content-end mt-4">
                            <!-- Approve Order -->
                            <form action="{{ route('admin.approveOrder', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success me-2">Approve Order</button>
                            </form>

                            <!-- Decline Order -->
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#declineOrderModal" style="display:inline;">Decline Order</button>
                        </div>

                        <!-- Decline Order Modal -->
                        <div class="modal fade" id="declineOrderModal" tabindex="-1" aria-labelledby="declineOrderModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="declineOrderModalLabel">Decline Order</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="declineOrderForm" action="{{ route('admin.declineOrder', $order->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="declineReason">Reason for Declining</label>
                                                <textarea name="decline_reason" id="declineReason" class="form-control" rows="5" placeholder="Enter the reason for declining this order" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger">Decline Order Request</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
