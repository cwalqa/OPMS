@extends('client.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Order Details</h5>
                        <div>
                            @switch(strtolower($order->status ?? 'pending'))
                                @case('pending')
                                    <span class="badge bg-warning text-dark fs-6"><i class="fas fa-clock me-1"></i> Pending</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-primary fs-6"><i class="fas fa-check me-1"></i> Approved</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success fs-6"><i class="fas fa-check-double me-1"></i> Completed</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger fs-6"><i class="fas fa-times me-1"></i> Cancelled</span>
                                    @break
								@case('declined')
                                    <span class="badge bg-danger fs-6"><i class="fas fa-times me-1"></i> Declined</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary fs-6">{{ ucfirst($order->status ?? 'Unknown') }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="print-logo text-center mb-4 d-none">
                            <img src="{{ asset('assets/img/logos/cwi.png') }}" alt="Company Logo" style="max-width: 200px;">
                        </div>

                        <!-- Order Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                @php
                                    $customer = \App\Models\QuickbooksCustomer::where('customer_id', session('customer.customer_id'))->first();
                                    $companyName = $customer ? $customer->company_name : 'Unknown Company';
                                @endphp
                                <label class="form-label fw-bold">Company Name</label>
                                <input type="text" class="form-control" value="{{ $companyName }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Purchase Order Number</label>
                                <input type="text" class="form-control" value="{{ $order->purchase_order_number }}" readonly>
                            </div>
							<div class="col-md-4">
                                <label class="form-label fw-bold">Client PO Number</label>
                                <input type="text" class="form-control" value="{{ $order->client_po_number }}" readonly>
                            </div>
						</div>
						<div class="row g-3 mb-4">
							<div class="col-md-6">
                                <label class="form-label fw-bold">Order Date</label>
                                <input type="text" class="form-control" value="{{ $order->po_date->format('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Final Invoice Amount</label>
                                <input type="text" class="form-control"  readonly>
								<!-- value="${{ number_format($order->total_amount, 2) }}" -->
                            </div>
                        </div>

                        <!-- Status Timeline -->
                        <div class="mb-4 print-exclude">
                            <h6 class="text-secondary mb-5 mt-5 text-center"><i class="fas fa-history me-2"></i>Order Timeline</h6>
                            <div class="position-relative pt-2 pb-3">
                                <div class="timeline-track"></div>
                                <div class="row g-0">
                                    <div class="col-3 text-center">
                                        <div class="timeline-point {{ in_array(strtolower($order->status ?? ''), ['pending', 'approved', 'completed']) ? 'active' : '' }}">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="mt-2 small">Created</div>
                                        <div class="smaller text-muted">{{ $order->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-3 text-center">
                                        <div class="timeline-point {{ in_array(strtolower($order->status ?? ''), ['approved', 'completed']) ? 'active' : '' }}">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="mt-2 small">Approved</div>
                                        <div class="smaller text-muted">
                                            @if(in_array(strtolower($order->status ?? ''), ['approved', 'completed']))
                                                {{ $order->approved_at ? $order->approved_at->format('M d, Y') : 'Pending' }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-3 text-center">
                                        <div class="timeline-point {{ strtolower($order->status ?? '') == 'completed' ? 'active' : '' }}">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="mt-2 small">Completed</div>
                                        <div class="smaller text-muted">
                                            @if(strtolower($order->status ?? '') == 'completed')
                                                {{ $order->completed_at ? $order->completed_at->format('M d, Y') : 'Pending' }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-3 text-center">
                                        <div class="timeline-point {{ strtolower($order->status ?? '') == 'cancelled' ? 'active-danger' : '' }}">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div class="mt-2 small">Cancelled</div>
                                        <div class="smaller text-muted">
                                            @if(strtolower($order->status ?? '') == 'cancelled')
                                                {{ $order->cancelled_at ? $order->cancelled_at->format('M d, Y') : '-' }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <h6 class="text-secondary mb-5 mt-5 text-center"><i class="fas fa-shopping-cart me-2"></i>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Product ID</th>
										<th>Description</th>
                                        <th>Quantity</th>
                                        <!-- <th>Unit Price</th>
                                        <th>Total Cost</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $orderTotal = 0;
                                    @endphp
                                    @foreach($order->items as $item)
                                    @php
                                        $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();
                                        $productName = $product ? $product->name : 'Unknown Product';
                                        $orderTotal += $item->amount;
                                    @endphp
                                        <tr>
                                            <td>{{ $productName }}</td>
                                            <td>{{ $item->sku }}</td>
											<td>{{ $item->description }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <!-- <td>${{ $item->unit_price }}</td>
                                            <td>${{ $item->amount }}</td> -->
                                        </tr>
                                    @endforeach
                                    <!-- <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">Order Total:</td>
                                        <td><u>${{ number_format($orderTotal, 2) }}</u></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>

						<!-- PO Document Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i> Attached PO Document</h6>
            </div>
            <div class="card-body">
                @if($order->po_document_path)
                    <div class="d-flex align-items-center">
                        @if(pathinfo($order->po_document_path, PATHINFO_EXTENSION) === 'pdf')
                            <i class="fas fa-file-pdf text-danger me-3 fs-2"></i>
                        @else
                            <i class="fas fa-file-alt text-primary me-3 fs-2"></i>
                        @endif
                        
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ basename($order->po_document_path) }}</h6>
                            <small class="text-muted">
                                Uploaded: {{ $order->created_at->format('M d, Y H:i') }} | 
                                {{ Str::upper(pathinfo($order->po_document_path, PATHINFO_EXTENSION)) }} File
                            </small>
                        </div>
                        
                        <div class="btn-group" role="group">
                            @if(pathinfo($order->po_document_path, PATHINFO_EXTENSION) === 'pdf')
                                <button class="btn btn-outline-secondary preview-pdf-btn"
                                        data-pdf-url="{{ asset('storage/' . $order->po_document_path) }}">
                                    <i class="fas fa-eye me-1"></i> Preview
                                </button>
                            @endif
                            
                            <a href="{{ asset('storage/' . $order->po_document_path) }}" 
                               class="btn btn-primary download-po-btn"
                               download="{{ basename($order->po_document_path) }}"
                               target="_blank">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-file-excel text-muted fs-1 mb-2"></i>
                        <p class="text-muted mb-0">No PO document attached</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

                        <!-- Notes -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Additional Notes</label>
                                <textarea class="form-control" rows="3" readonly>{{ $order->customer_memo }}</textarea>
                            </div>
                            @if(strtolower($order->status ?? '') == 'cancelled' && $order->cancel_reason)
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">Cancellation Reason</label>
                                <textarea class="form-control border-danger" rows="3" readonly>{{ $order->cancel_reason }}</textarea>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-5 print-exclude">
                            <a href="{{ route('client.purchaseOrderHistory') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Order History
                            </a>
                            <div>
                                <button type="button" class="btn btn-primary" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Print / Save as PDF
                                </button>
                                
                                @if(strtolower($order->status ?? '') == 'pending')
                                    @php
                                        $isExpired = $order->created_at->addHours(48)->isPast();
                                    @endphp
                                    <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#modifyOrderModal" {{ $isExpired ? 'disabled' : '' }}>
                                        <i class="fas fa-edit me-1"></i> Modify Order
                                    </button>
                                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal" {{ $isExpired ? 'disabled' : '' }}>
                                        <i class="fas fa-trash me-1"></i> Cancel Order
                                    </button>
                                    
                                @endif
                                
                                @if(strtolower($order->status ?? '') == 'approved')
                                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                        <i class="fas fa-times-circle me-1"></i> Request Cancellation
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modify Order Modal -->
    @include('client.partials.modify-order')

    <!-- Cancel Order Modal -->
    @include('client.partials.cancel-order')

	<!-- PO Preview Modal -->
    @include('client.partials.po-preview')
	
@endsection

@section('styles')
    <!-- Custom CSS for timeline and print view -->
    <style>
        /* Timeline Styling */
        .timeline-track {
            position: absolute;
            top: 15px;
            left: 5%;
            right: 5%;
            height: 3px;
            background-color: #e9ecef;
            z-index: 1;
        }
        
        .timeline-point {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #f8f9fa;
            border: 3px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .timeline-point.active {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }
        
        .timeline-point.active-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .smaller {
            font-size: 0.75rem;
        }

        /* Print styling */
        @media print {
            /* Hide general UI chrome */
            header, footer, nav, .navbar, .sidebar, .modal, .btn, .print-exclude,
            .card-header, .card-footer, .alert, .breadcrumbs, .actions-bar {
                display: none !important;
            }

            /* Hide the whole body and selectively show what's important */
            body * {
                visibility: hidden;
            }

            /* Only show the card-body and its contents */
            .card-body, .card-body * {
                visibility: visible;
            }

            /* Fix layout issues */
            .card-body {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 2rem;
            }

            /* Beautify table printing */
            table {
                width: 100% !important;
                border-collapse: collapse;
            }

            table th, table td {
                border: 1px solid #333;
                padding: 8px;
                font-size: 14px;
            }

            /* Optional: white background for legibility */
            body {
                background: white !important;
            }

            .print-logo {
                display: block !important;
                visibility: visible !important;
            }

            .print-logo img {
                max-width: 200px;
                margin-bottom: 20px;
            }
        }

		/* Download button styles */
        .download-po-btn {
            transition: all 0.2s ease;
        }
        
        .download-po-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* PDF preview modal styling */
        #pdfPreviewModal .modal-content {
            height: 85vh;
        }
        
        /* File type icons */
        .fa-file-pdf {
            color: #e74c3c;
        }
        
        .fa-file-word {
            color: #2b579a;
        }
        
        .fa-file-excel {
            color: #217346;
        }
        
        .fa-file-alt {
            color: #6c757d;
        }



    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- JavaScript for Order Management -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const orderItemsBody = document.getElementById("orderItemsBody");
            const newItemsBody = document.getElementById("newItemsBody");
            const orderTotalField = document.getElementById("orderTotal");
            const addNewItemButton = document.getElementById("addNewItemButton");
            const removedItemsContainer = document.getElementById("removedItemsContainer");
            
            // Function to calculate row total and overall total
            function calculateTotals() {
                let overallTotal = 0;
                document.querySelectorAll(".item-total").forEach((totalField) => {
                    overallTotal += parseFloat(totalField.value) || 0;
                });
                if (orderTotalField) {
                    orderTotalField.value = overallTotal.toFixed(2);
                }
            }
            
            // Handle quantity or product change
            document.addEventListener("input", (e) => {
                if (e.target.closest(".item-quantity") || e.target.closest(".product-select")) {
                    const row = e.target.closest("tr");
                    const quantity = parseFloat(row.querySelector(".item-quantity").value) || 0;
                    const productSelect = row.querySelector("select");
                    if (!productSelect) return;
                    
                    const selectedOption = productSelect.selectedOptions[0];
                    if (!selectedOption) return;
                    
                    const unitPrice = parseFloat(selectedOption.dataset.rate) || 0;

                    // Update SKU and Unit Price fields if they exist
                    const skuField = row.querySelector("[name$='[sku]']");
                    const unitPriceField = row.querySelector(".item-unit-price");
                    const totalField = row.querySelector(".item-total");
                    
                    if (skuField) skuField.value = selectedOption.value;
                    if (unitPriceField) unitPriceField.value = unitPrice.toFixed(2);
                    if (totalField) totalField.value = (quantity * unitPrice).toFixed(2);

                    calculateTotals();
                }
            });
            
            // Add a new item row
            if (addNewItemButton) {
                addNewItemButton.addEventListener("click", () => {
                    const uniqueId = Date.now();
                    const newRow = `
                        <tr>
                            <td>
                                <select name="new_items[${uniqueId}][product_id]" class="form-select product-select">
                                    <option value="">Select Product</option>
                                    @foreach ($items as $availableItem)
                                        <option value="{{ $availableItem->item_id }}" data-rate="{{ $availableItem->unit_price }}">
                                            {{ $availableItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="new_items[${uniqueId}][sku]" class="form-control" readonly></td>
                            <td><input type="number" name="new_items[${uniqueId}][quantity]" class="form-control item-quantity" min="1" value="1"></td>
                            <td><input type="text" name="new_items[${uniqueId}][unit_price]" class="form-control item-unit-price" readonly></td>
                            <td><input type="text" name="new_items[${uniqueId}][total]" class="form-control item-total" readonly></td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                    newItemsBody.insertAdjacentHTML("beforeend", newRow);
                });
            }

            // Remove a row
            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    const btn = e.target.closest('.remove-row');
                    const row = btn.closest('tr');
                    const itemId = btn.getAttribute('data-item-id');

                    // If existing item, add to removed items container
                    if (itemId && removedItemsContainer) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'removed_items[]';
                        hiddenInput.value = itemId;
                        removedItemsContainer.appendChild(hiddenInput);
                    }

                    // Remove the row visually
                    row.remove();

                    // Recalculate totals after row removal
                    calculateTotals();
                }
            });
            
            // Handle cancel order confirmation
            const cancelOrderForm = document.getElementById('cancelOrderForm');
            
            if (cancelOrderForm) {
                cancelOrderForm.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent default form submission
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you really want to cancel this order?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, Cancel it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit(); // Submit the form if confirmed
                        }
                    });
                });
            }

            // Show success or error alerts based on session messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Try Again'
                });
            @endif


			// PDF Preview functionality
    document.body.addEventListener('click', function(e) {
        // Check if clicked element is a preview button or its child
        const previewBtn = e.target.closest('.preview-pdf-btn');
        if (previewBtn) {
            e.preventDefault();
            const pdfUrl = previewBtn.getAttribute('data-pdf-url');
            const pdfFrame = document.getElementById('pdfPreviewFrame');
            const downloadLink = document.getElementById('fullPdfDownload');
            
            // Set PDF source and download link
            pdfFrame.src = pdfUrl + '#toolbar=0&navpanes=0';
            downloadLink.href = pdfUrl;
            
            // Initialize and show modal
            const pdfModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
            pdfModal.show();
            
            // Reset iframe when modal is hidden
            document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function() {
                pdfFrame.src = '';
            });
        }
    });

    // Add tooltips for download buttons
    const downloadButtons = document.querySelectorAll('.download-po-btn');
    downloadButtons.forEach(btn => {
        new bootstrap.Tooltip(btn, {
            title: 'Download PO Document',
            placement: 'top'
        });
    });
        });
    </script>

@endsection