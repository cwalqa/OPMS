@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Review Purchase Orders</h6>
                        <!-- Search Input -->
                        <input type="text" id="orderSearchInput" placeholder="Search..." class="form-control w-25 me-3">
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0" id="orderTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Order Date</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Purchase Order Number</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Customer Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Total Amount</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Status</th>
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
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                    <td>
                                        <!-- View Order Button -->
                                        <a href="{{ route('admin.viewOrderDetails', $order->id) }}" class="btn btn-secondary btn-sm">
                                            View Order
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('orderSearchInput');
        const table = document.getElementById('orderTable');
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
