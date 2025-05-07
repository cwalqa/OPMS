@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-warehouse"></i> Warehouse Management</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                        <i class="fas fa-plus"></i> Add New Warehouse
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search Input -->
                    <div class="mb-3">
                        <input type="text" id="warehouseSearchInput" class="form-control" placeholder="Search Warehouses...">
                    </div>

                    <!-- Warehouse Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="warehouseTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Capacity</th>
                                    <th>Lots</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouses as $warehouse)
                                    <tr>
                                        <td><strong>{{ $warehouse->name }}</strong></td>
                                        <td>{{ $warehouse->location ?? 'N/A' }}</td>
                                        <td><span class="badge bg-secondary">{{ $warehouse->capacity ?? 'N/A' }}</span></td>
                                        <td>
                                            @if($warehouse->lots)
                                                <span class="text-muted">{{ implode(', ', explode(',', $warehouse->lots)) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editWarehouseModal{{ $warehouse->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.inventory.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Warehouse Modal -->
                                    <div class="modal fade" id="editWarehouseModal{{ $warehouse->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Warehouse</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('admin.inventory.warehouses.update', $warehouse->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label class="form-label">Warehouse Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $warehouse->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Location</label>
                                                            <input type="text" name="location" class="form-control" value="{{ $warehouse->location }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Capacity</label>
                                                            <input type="number" name="capacity" class="form-control" value="{{ $warehouse->capacity }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Lots (comma-separated)</label>
                                                            <input type="text" name="lots" class="form-control" value="{{ $warehouse->lots }}">
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Update Warehouse</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Edit Modal -->
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.inventory.warehouses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Warehouse Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lots (comma-separated)</label>
                        <input type="text" name="lots" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Warehouse</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("warehouseSearchInput");
    const tableRows = document.querySelectorAll("#warehouseTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
});
</script>
@endsection
