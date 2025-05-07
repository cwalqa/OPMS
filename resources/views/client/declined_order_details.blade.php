@extends('client.app')

@section('content')
	<div class="container-fluid py-4">
		<div class="row">
			<div class="col-12">
				<div class="card my-4">
					<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
						<div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
							<h6 class="text-white text-capitalize ps-3">DECLINED ORDER DETAILS</h6>
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
										<label for="order_date"><b>Order Date/Time</b></label>
										<input type="text" name="order_date" id="order_date" class="form-control" value="{{ $order->created_at->format('Y-m-d H:i:s') }}" readonly>
									</div>
								</div>
                                <div class="col-md-3">
									<div class="form-group">
										<label for="decline_date"><b>Declined Date/Time</b></label>
										<input type="text" name="decline_date" id="decline_date" class="form-control" value="{{ $order->updated_at->format('Y-m-d H:i:s') }}" readonly>
									</div>
								</div>
								<!-- <div class="col-md-3">
									<div class="form-group">
										<label for="total_amount"><b>Total Amount</b></label>
										<input type="text" name="total_amount" id="total_amount" class="form-control" value="${{ $order->total_amount }}" readonly>
									</div>
								</div> -->
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
										<label for="additional_notes"><b>Order Notes</b></label>
										<textarea id="additional_notes" class="form-control" rows="2" readonly>{{ $order->customer_memo }}</textarea>
									</div>
								</div>
							</div>

                            <div class="row mt-2">
								<div class="col-md-12">
									<div class="form-group">
										<label for="decline_reason"><b>Reason For Decline</b></label>
										<textarea id="decline_reason" class="form-control" rows="2" readonly>{{ $order->decline_reason }}</textarea>
									</div>
								</div>
							</div>
							<!-- Action Buttons -->
							<div class="row mt-4">
								<div class="col-md-6 text-start">
									<a href="{{ route('client.declinedOrderHistory') }}" class="btn btn-secondary">
										Back to Declined Orders
									</a>
								</div>
								<div class="col-md-6 text-end">
									<button type="button" class="btn btn-primary" onclick="window.print()">
										Print / Save as PDF
									</button>
									<!-- <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#modifyOrderModal">
										Modify Order
									</button>
									<button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
										Cancel Order
									</button> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

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
			
		});
	</script>


	<!-- Custom CSS for print view -->
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


	@endsection
