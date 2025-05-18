@extends('client.app')

@section('content')
	<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Order Details</h5>
                </div>
                <div class="card-body p-4">

                    <!-- Order Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
							@php
								$customer = \App\Models\QuickbooksCustomer::where('customer_id', session('customer.customer_id'))->first();
								$companyName = $customer ? $customer->company_name : 'Unknown Company';
							@endphp
                            <label class="form-label fw-bold">Company Name</label>
                            <input type="text" class="form-control" value="{{ $companyName }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Purchase Order Number</label>
                            <input type="text" class="form-control" value="{{ $order->purchase_order_number }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Order Date</label>
                            <input type="text" class="form-control" value="{{ $order->created_at->format('Y-m-d') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Amount</label>
                            <input type="text" class="form-control" value="${{ number_format($order->total_amount, 2) }}" readonly>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h6 class="text-center mb-4">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Product ID</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Cost</th>
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
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ $item->unit_price }}</td>
                                        <td>${{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="4" class="text-end fw-bold">Order Total:</td>
                                    <td><u>${{ number_format($orderTotal, 2) }}</u></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">Additional Notes</label>
                        <textarea class="form-control" rows="3" readonly>{{ $order->customer_memo }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <a href="{{ route('client.purchaseOrderHistory') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Order History
                        </a>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> Print / Save as PDF
                            </button>
							@php
								$isExpired = $order->created_at->addHours(48)->isPast();
							@endphp
                            <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#modifyOrderModal" {{ $isExpired ? 'disabled' : '' }}>
                                <i class="fas fa-edit me-1"></i> Modify Order
                            </button>
                            <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal" {{ $isExpired ? 'disabled' : '' }}>
                                <i class="fas fa-trash me-1"></i> Cancel Order
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



	<!-- Modify Order Modal -->
	<!-- Modify Order Modal -->
	<div class="modal fade" id="modifyOrderModal" tabindex="-1" aria-labelledby="modifyOrderModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content shadow-lg border-0 rounded-3">
				<div class="modal-header bg-gradient-primary text-white">
					<h5 class="modal-title">
						<i class="fas fa-edit me-2"></i> Modify Your Order
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="modifyOrderForm" action="{{ route('client.updateOrder', $order->id) }}" method="POST">
						@csrf
						@method('PUT')

						<!-- Hidden container for tracking removed items -->
						<div id="removedItemsContainer"></div>

						<h6 class="mb-3">Current Order Items</h6>
						<div class="table-responsive rounded shadow-sm">
							<table class="table table-hover align-middle mb-0">
								<thead class="table-dark text-white">
									<tr>
										<th>Product/Service</th>
										<th>Product ID</th>
										<th>Quantity</th>
										<th>Unit Price</th>
										<th>Total Cost</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody id="orderItemsBody">
									@foreach($order->items as $item)
										@php
											$product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();
											$productName = $product ? $product->name : 'Unknown Product';
										@endphp
										<tr data-item-id="{{ $item->id }}">
											<td>
												<select name="items[{{ $item->id }}][product_id]" class="form-select">
													@foreach ($items as $availableItem)
														<option value="{{ $availableItem->item_id }}" data-rate="{{ $availableItem->unit_price }}" {{ $item->sku == $availableItem->item_id ? 'selected' : '' }}>
															{{ $availableItem->name }}
														</option>
													@endforeach
												</select>
											</td>
											<td><input type="text" name="items[{{ $item->id }}][sku]" class="form-control" value="{{ $item->sku }}" readonly></td>
											<td><input type="number" name="items[{{ $item->id }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity }}" min="1"></td>
											<td><input type="text" name="items[{{ $item->id }}][unit_price]" class="form-control item-unit-price" value="{{ $item->unit_price }}" readonly></td>
											<td><input type="text" name="items[{{ $item->id }}][total]" class="form-control item-total" value="{{ $item->amount }}" readonly></td>
											<td>
												<button type="button" class="btn btn-outline-danger btn-sm remove-row" data-item-id="{{ $item->id }}">
													<i class="fas fa-trash"></i>
												</button>
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot class="table-light">
									<tr>
										<td colspan="4" class="text-end fw-bold">Total Amount:</td>
										<td><input type="text" name="total_amount" id="orderTotal" class="form-control" value="{{ $order->total_amount }}" readonly></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>

						<!-- Add New Items -->
						<h6 class="mt-5 mb-3">Add New Items</h6>
						<div class="table-responsive rounded shadow-sm">
							<table class="table table-hover align-middle mb-0">
								<tbody id="newItemsBody"></tbody>
							</table>
							<button type="button" id="addNewItemButton" class="btn btn-success btn-sm mt-3">
								<i class="fas fa-plus-circle me-1"></i> Add Item
							</button>
						</div>

						<!-- Notes -->
						<div class="mt-4">
							<label for="customer_memo" class="form-label"><b>Additional Notes</b></label>
							<textarea name="customer_memo" id="customer_memo" class="form-control" rows="3">{{ $order->customer_memo }}</textarea>
						</div>

						<!-- Save Button -->
						<div class="d-flex justify-content-end mt-4">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save me-1"></i> Save Changes
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!-- Cancel Order Modal -->
	<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content shadow-lg border-0 rounded-3">
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title" id="cancelOrderModalLabel">
						<i class="fas fa-exclamation-triangle me-2"></i> Cancel Order Confirmation
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="cancelOrderForm" action="{{ route('client.cancelOrder', $order->id) }}" method="POST">
						@csrf
						<div class="mb-3">
							<label for="cancelReason" class="form-label">Reason for Cancellation</label>
							<textarea name="cancel_reason" id="cancelReason" class="form-control" rows="4" placeholder="Briefly explain why you wish to cancel this order..." required></textarea>
						</div>
						<div class="d-flex justify-content-end gap-2 mt-4">
							<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
								<i class="fas fa-times-circle me-1"></i> No, Keep My Order
							</button>
							<button type="submit" class="btn btn-danger">
								<i class="fas fa-trash-alt me-1"></i> Yes, Cancel Order
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- JavaScript for Adding Items -->
	<script>
		document.addEventListener("DOMContentLoaded", function () {
		const orderItemsBody = document.getElementById("orderItemsBody");
		const newItemsBody = document.getElementById("newItemsBody");
		const orderTotalField = document.getElementById("orderTotal");
			// Function to calculate row total and overall total
			function calculateTotals() {
				let overallTotal = 0;
				document.querySelectorAll(".item-total").forEach((totalField) => {
					overallTotal += parseFloat(totalField.value) || 0;
				});
				orderTotalField.value = overallTotal.toFixed(2);
			}
			// Handle quantity or product change
			document.addEventListener("input", (e) => {
				if (e.target.closest(".item-quantity") || e.target.closest(".product-select")) {
					const row = e.target.closest("tr");
					const quantity = parseFloat(row.querySelector(".item-quantity").value) || 0;
					const productSelect = row.querySelector(".product-select");
					const selectedOption = productSelect.selectedOptions[0];
					const unitPrice = parseFloat(selectedOption.dataset.rate) || 0;

					// Update SKU and Unit Price fields
					row.querySelector("[name$='[sku]']").value = selectedOption.value;
					row.querySelector(".item-unit-price").value = unitPrice.toFixed(2);

					// Calculate and update total cost for the row
					row.querySelector(".item-total").value = (quantity * unitPrice).toFixed(2);

					calculateTotals();
				}
			});
			// Add a new item row
			document.getElementById("addNewItemButton").addEventListener("click", () => {
				const uniqueId = Date.now();
				const newRow = `
					<tr>
						<td>
							<select name="new_items[${uniqueId}][product_id]" class="form-control product-select">
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
						<td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
					</tr>`;
				newItemsBody.insertAdjacentHTML("beforeend", newRow);
			});

			// Remove a row
			document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) {
        const btn = e.target.closest('.remove-row');
        const row = btn.closest('tr');
        const itemId = btn.getAttribute('data-item-id');

        // If existing item, add to removed items container
        if (itemId) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'removed_items[]';
            hiddenInput.value = itemId;
            document.getElementById('removedItemsContainer').appendChild(hiddenInput);
        }

        // Remove the row visually
        row.remove();

        // Recalculate totals after row removal
        calculateTotals();
    }
});
		});
	</script>


	<!-- Custom CSS for print view -->
		<style>
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
				
				/* Hide elements not needed in the print view */
				.btn-primary, /* Print/Save as PDF button */
				.btn-warning, /* Modify Order button */
				.btn-danger, /* Cancel Order button */
				.navbar, /* Navigation bar (e.g., Dashboard) */
				.welcome-section { /* Replace with the class or ID for the "Welcome Back" section */
					display: none !important;
				}
			}
		</style>



<script>
    document.addEventListener("DOMContentLoaded", function () {
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
    });
</script>


	@endsection
