@extends('client.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center p-4">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Purchase Order History
                    </h5>
                    <div class="w-25">
                        <input type="text" id="purchaseOrderSearchInput" class="form-control form-control-sm" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="purchaseOrderTable">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Order Date</th>
                                    <th>PO Number</th>
                                    <th>Total Amount</th>
                                    <th>Notes</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $order)
                                    <tr>
                                        <td>{{ $purchaseOrders->firstItem() + $loop->index }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $order->purchase_order_number }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{ $order->customer_memo }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('client.viewOrderDetails', $order->id) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    

                    <div class="d-flex justify-content-center mt-3">
                        {{ $purchaseOrders->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('purchaseOrderSearchInput');
        const table = document.getElementById('purchaseOrderTable');
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
