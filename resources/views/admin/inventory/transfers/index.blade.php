@extends('admin.app')

@section('title', 'Inventory Transfers')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Inventory Transfers</h5>
                    <a href="{{ route('admin.inventory.transfers.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> New Transfer
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search Input -->
                    <div class="mb-3">
                        <input type="text" id="transferSearchInput" class="form-control" placeholder="Search Transfers...">
                    </div>

                    <!-- Transfers Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="transferTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Quantity</th>
                                    <th>User</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $transfer)
                                    <tr>
                                        <td><strong>#{{ $transfer->id }}</strong></td>
                                        <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $transfer->item->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $transfer->sourceWarehouse->name }}</span>
                                            @if($transfer->source_lot_shelf)
                                                <small class="text-muted d-block">Lot: {{ $transfer->source_lot_shelf }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $transfer->destinationWarehouse->name }}</span>
                                            @if($transfer->destination_lot_shelf)
                                                <small class="text-muted d-block">Lot: {{ $transfer->destination_lot_shelf }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-primary">{{ $transfer->quantity }}</span></td>
                                        <td>{{ $transfer->user->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.inventory.transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No transfers found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("transferSearchInput");
    const tableRows = document.querySelectorAll("#transferTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
});
</script>
@endsection
