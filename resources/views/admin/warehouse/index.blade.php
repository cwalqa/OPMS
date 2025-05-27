@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center px-4 py-3">
            <h5 class="mb-0">
                <i class="fas fa-warehouse me-2"></i> Warehouses Management
            </h5>
            <button class="btn btn-light text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createWarehouseModal">
                <i class="fas fa-plus me-1"></i> Add Warehouse
            </button>
        </div>

        <div class="card-body px-4 py-3">
            {{-- Success Alert --}}
            @if(session('success'))
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('success')),
                            timer: 3000,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif

            @if($warehouses->isEmpty())
                <div class="alert alert-light mb-0">
                    <i class="fas fa-info-circle me-2"></i> No warehouses found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->code }}</td>
                                    <td>{{ $warehouse->location ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $warehouse->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editWarehouseModal{{ $warehouse->id }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $warehouse->id }}, '{{ $warehouse->name }}')">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>

                                        <form id="deleteForm{{ $warehouse->id }}"
                                              action="{{ route('admin.warehouse.destroy', $warehouse->id) }}"
                                              method="POST"
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                {{-- Include edit modal --}}
                                @include('admin.warehouse.partials.edit-warehouse-modal', ['warehouse' => $warehouse])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Include create modal --}}
@include('admin.warehouse.partials.create-warehouse-modal')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Delete "' + name + '"?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm' + id);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire('Error', 'Form not found.', 'error');
                }
            }
        });
    }
</script>
@endsection
