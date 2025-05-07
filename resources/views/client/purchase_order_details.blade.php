@extends('client.app')

@section('content')
	<div class="container-fluid py-4">
		<div class="row">
			<div class="col-12">
				<div class="card my-4">
					<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
						<div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
							<h6 class="text-white text-capitalize ps-3">ORDER DETAILS</h6>
						</div>
					</div>
					<div class="card-body px-0 pb-2">
						<div class="table-responsive p-3">
							<!-- Order Information -->
							<div class="row d-flex justify-content-center align-items-center mb-3 order-info-row">
								<div class="col-md-3">
									<div class="form-group">
										@php
											$customer = \App\Models\QuickbooksCustomer::where('customer_id', session('customer.customer_id'))->first();
											$companyName = $customer ? $customer->company_name : 'Unknown Company';
										@endphp
										<label for="company_name"><b>Company Name</b></label>
										<input type="text" name="company_name" id="company_name" class="form-control" value="{{ $companyName }}" readonly>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="purchase_order_number"><b>Purchase Order Number</b></label>
										<input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control" value="{{ $order->purchase_order_number }}" readonly>
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
										<input type="text" name="total_amount" id="total_amount" class="form-control" value="${{ $order->total_amount }}" readonly>
									</div>
								</div>
							</div>
							<!-- Order Items Table -->
							<h6 class="text-center mt-5 mb-3"><b>ORDER ITEMS</b></h6>
							<div class="table-responsive">
								<table class="table align-items-center mb-0">
									<thead>
										<tr>
											<th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Product Name</th>
											<th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Product ID</th>
											<th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Quantity</th>
											<th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Unit Price</th>
											<th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Total Cost</th>
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
												<p class="text-xs font-weight-bold mb-0">${{ $item->unit_price }}</p>
											</td>
											<td>
												<p class="text-xs font-weight-bold mb-0">${{ $item->amount }}</p>
											</td>
										</tr>
										@endforeach
										<tr>
											<td colspan="4" class="text-end">
												<strong>Order Total:</strong>
											</td>
											<td>
												<p class="text-sm font-weight-bold mb-0"><u>$ {{ number_format($orderTotal, 2) }}</u></p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- Additional Notes -->
							<div class="row mt-2">
								<div class="col-md-12">
									<div class="form-group">
										<label for="additional_notes"><b>Additional Notes</b></label>
										<textarea id="additional_notes" class="form-control" rows="2" readonly>{{ $order->customer_memo }}</textarea>
									</div>
								</div>
							</div>
							<!-- Action Buttons -->
							<div class="row mt-4">
								<div class="col-md-6 text-start">
									<a href="{{ route('client.purchaseOrderHistory') }}" class="btn btn-secondary">
										Back to Order History
									</a>
								</div>
								<div class="col-md-6 text-end">
									<button type="button" class="btn btn-primary" onclick="window.print()">
										Print / Save as PDF
									</button>
									@php
										$isExpired = $order->created_at->addHours(48)->isPast();
									@endphp
									<button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#modifyOrderModal" {{ $isExpired ? 'disabled' : '' }}>
										Modify Order
									</button>
									<button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal" {{ $isExpired ? 'disabled' : '' }}>
										Cancel Order
									</button>
								</div>
							</div>
							<!-- Modify Order Modal -->
							<div class="modal fade" id="modifyOrderModal" tabindex="-1" aria-labelledby="modifyOrderModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="modifyOrderModalLabel">Modify Order</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<div class="modal-body">
											<form id="modifyOrderForm" action="{{ route('client.updateOrder', $order->id) }}" method="POST">
												@csrf
												@method('PUT')
												<!-- Existing Items -->
												<h6>Order Items</h6>
												<div class="table-responsive">
													<table class="table align-items-center mb-0">
														<thead>
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
															<tr>
																<td>
																	<select name="items[{{ $item->id }}][product_id]" class="form-control product-select">
																		@foreach ($items as $availableItem)
																		<option value="{{ $availableItem->item_id }}" 
																			data-rate="{{ $availableItem->unit_price }}" 
																			{{ $item->sku == $availableItem->item_id ? 'selected' : '' }}>
																			{{ $availableItem->name }}
																		</option>
																		@endforeach
																	</select>
																</td>
																<td>
																	<input type="text" name="items[{{ $item->id }}][sku]" class="form-control" value="{{ $item->sku }}" readonly>
																</td>
																<td>
																	<input type="number" name="items[{{ $item->id }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity }}" min="1">
																</td>
																<td>
																	<input type="text" name="items[{{ $item->id }}][unit_price]" class="form-control item-unit-price" value="{{ $item->unit_price }}" readonly>
																</td>
																<td>
																	<input type="text" name="items[{{ $item->id }}][total]" class="form-control item-total" value="{{ $item->amount }}" readonly>
																</td>
																<td>
																	<button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
																</td>
															</tr>
															@endforeach
														</tbody>
														<tfoot>
															<tr>
																<td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
																<td>
																	<input type="text" name="total_amount" id="orderTotal" class="form-control" value="{{ $order->total_amount }}" readonly>
																</td>
																<td></td>
															</tr>
														</tfoot>
													</table>
												</div>
												<!-- Add New Items -->
												<h6 class="mt-4">Add New Items</h6>
												<div class="table-responsive">
													<table class="table align-items-center mb-0">
														<tbody id="newItemsBody">
															<!-- New items will be dynamically added here -->
														</tbody>
													</table>
													<button type="button" id="addNewItemButton" class="btn btn-success btn-sm mt-2">Add Item</button>
												</div>
												<!-- Additional Notes -->
												<div class="row mt-4">
													<div class="col-md-12">
														<label for="customer_memo"><b>Additional Notes</b></label>
														<textarea name="customer_memo" class="form-control">{{ $order->customer_memo }}</textarea>
													</div>
												</div>
												<!-- Save Button -->
												<div class="mt-3">
													<button type="submit" class="btn btn-primary">Save Changes</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Cancel Order Modal -->
							<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<div class="modal-body">
											<form id="cancelOrderForm" action="{{ route('client.cancelOrder', $order->id) }}" method="POST">
												@csrf
												<div class="form-group mb-3">
													<label for="cancelReason">Reason for Cancellation</label>
													<textarea name="cancel_reason" id="cancelReason" class="form-control" rows="5" placeholder="Enter the reason for canceling this order" required></textarea>
												</div>
												<button type="submit" class="btn btn-danger">Cancel Order</button>
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
			document.addEventListener("click", (e) => {
				if (e.target.closest(".remove-row")) {
					e.target.closest("tr").remove();
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
