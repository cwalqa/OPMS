@extends('client.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Purchase Order History
                    </h5>
                    <div class="d-flex gap-2 w-50">
                        <select id="statusFilter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="canceled">Canceled</option>
                            <option value="completed">Completed</option>
                        </select>
                        <input type="text" id="purchaseOrderSearchInput" class="form-control form-control-sm" placeholder="Search...">
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0" id="purchaseOrderTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>PO Number</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Timeline</th>
                                    <th>Notes</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $order)
                                    <tr data-status="{{ strtolower($order->status) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">{{ $order->purchase_order_number }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @php $status = strtolower($order->status); @endphp
                                            <span class="badge rounded-pill 
                                                @if($status == 'pending') bg-warning text-dark
                                                @elseif($status == 'approved') bg-success
                                                @elseif($status == 'rejected') bg-danger
                                                @elseif($status == 'completed') bg-info
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                <i class="fas fa-circle {{ $status === 'pending' ? 'text-warning' : 'text-muted' }}"></i>
                                                <i class="fas fa-arrow-right text-muted"></i>
                                                <i class="fas fa-circle {{ $status === 'approved' ? 'text-success' : 'text-muted' }}"></i>
                                                <i class="fas fa-arrow-right text-muted"></i>
                                                <i class="fas fa-circle {{ $status === 'completed' ? 'text-info' : 'text-muted' }}"></i>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $order->customer_memo }}</td>
                                        <td class="text-end">
                                            @if($status === 'completed')
                                                <span class="fw-semibold">${{ number_format($order->total_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted small">Pending completion</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('client.viewOrderDetails', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination removed for full data --}}
                    {{-- <div class="d-flex justify-content-center mt-4"> --}}
                        {{-- {{ $purchaseOrders->links() }} --}}
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setupPOFilters() {
        const searchInput = document.getElementById('purchaseOrderSearchInput');
        const statusFilter = document.getElementById('statusFilter');
        const table = document.getElementById('purchaseOrderTable');

        const filterTable = () => {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            let visibleCount = 0;

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return;

                const rowStatus = row.getAttribute('data-status');
                const statusMatch = !statusValue || rowStatus === statusValue;
                const textMatch = [...row.querySelectorAll('td')].some(td =>
                    td.textContent.toLowerCase().includes(searchTerm)
                );

                if (statusMatch && textMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            let noResultsRow = table.querySelector('tr.no-results');
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results';
                noResultsRow.innerHTML = '<td colspan="8" class="text-center text-muted">No matching orders found.</td>';
                table.querySelector('tbody').appendChild(noResultsRow);
            }

            noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
        };

        searchInput.addEventListener('input', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }

    document.addEventListener('DOMContentLoaded', setupPOFilters);
</script>
@endpush
