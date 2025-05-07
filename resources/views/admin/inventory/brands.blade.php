@extends('admin.app')

@section('title', 'Product Brands')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-copyright"></i> Product Brands</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                        <i class="fas fa-plus"></i> Add New Brand
                    </button>
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
                        <input type="text" id="brandSearchInput" class="form-control" placeholder="Search Brands...">
                    </div>

                    <!-- Brands Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="brandTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td><strong>{{ $brand->name }}</strong></td>
                                        <td>{{ $brand->description ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBrandModal{{ $brand->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.inventory.brands.destroy', $brand->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Brand Modal -->
                                    <div class="modal fade" id="editBrandModal{{ $brand->id }}" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Brand</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('admin.inventory.brands.update', $brand->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label class="form-label">Brand Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea name="description" class="form-control">{{ $brand->description }}</textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary w-100">Update Brand</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Edit Modal -->
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No brands found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.inventory.brands.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Brand Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Brand</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("brandSearchInput");
    const tableRows = document.querySelectorAll("#brandTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
});
</script>
@endsection
