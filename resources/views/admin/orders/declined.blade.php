@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Declined Purchase Orders</h6>
                        <!-- Search Input -->
                        <input type="text" id="declinedOrderSearchInput" placeholder="Search..." class="form-control w-25 me-3">
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0" id="declinedOrderTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Order Date</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Purchase Order Number</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Customer Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Total Amount</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Declined By</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->created_at->format('Y-m-d') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->purchase_order_number }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->customer->fully_qualified_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">${{ number_format($order->total_amount, 2) }}</p>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold mb-0 badge bg-info">{{ ucfirst($order->status) }}</span> <!-- Display status -->
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $order->approved_by ? App\Models\QuickbooksAdmin::find($order->approved_by)->name : 'N/A' }}</p>
                                    </td>
                                    <td>
                                        <!-- View Approved Order Details -->
                                        <a href="{{ route('admin.viewDeclinedOrderDetails', $order->id) }}" class="btn btn-secondary btn-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('declinedOrderSearchInput');
        const table = document.getElementById('declinedOrderTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) { // Skip header row
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
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

			/* Hide the Print Order button */
			.btn-primary {
			display: none !important;
			}

			/* Hide the Dashboard navigation link */
			a.nav-link[href*="dashboard"] {
			display: none !important;
			}

			/* Hide the Welcome Back message */
			.nav-link.text-body {
			display: none !important;
			}

			/* Hide the fixed plugin (settings button and options) */
			.fixed-plugin {
			display: none !important;
			}

			/* Additional styles to ensure proper print formatting */
			aside.sidenav, /* Hide the sidebar */
			.navbar,       /* Hide the top navbar */
			.footer {      /* Hide the footer if present */
			display: none !important;
			}

			/* Adjust the main content area to take full width */
			main.main-content {
			margin-left: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			}
		}
	</style>
@endsection