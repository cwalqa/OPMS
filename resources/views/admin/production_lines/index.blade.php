@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Manage Production Lines</h6>
                        <!-- Button to trigger Add Production Line Modal -->
                        <button class="btn btn-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addProductionLineModal">
                            Add New Production Line
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <input type="text" id="productionLineSearchInput" placeholder="Search Production Lines..." class="form-control mb-3">
                        
                        <table class="table align-items-center mb-0" id="productionLineTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Line Name</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Max Quantity</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Line Manager</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Status</th>
                                    <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-10 ps-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productionLines as $line)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $line->line_name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $line->max_quantity }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $line->lineManager->name }}</p>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($line->line_status == 'available') bg-success
                                            @elseif($line->line_status == 'in production') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($line->line_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editProductionLineModal{{ $line->id }}">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteProductionLineModal{{ $line->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Production Line Modal -->
                                <div class="modal fade" id="editProductionLineModal{{ $line->id }}" tabindex="-1" aria-labelledby="editProductionLineModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editProductionLineModalLabel">Edit Production Line</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('admin.editProductionLine', $line->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="line_name">Line Name</label>
                                                        <input type="text" class="form-control" name="line_name" value="{{ $line->line_name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="max_quantity">Max Quantity</label>
                                                        <input type="number" class="form-control" name="max_quantity" value="{{ $line->max_quantity }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="line_manager_id">Line Manager</label>
                                                        <select class="form-control" name="line_manager_id" required>
                                                            @foreach($lineManagers as $manager)
                                                                <option value="{{ $manager->id }}" {{ $manager->id == $line->line_manager_id ? 'selected' : '' }}>
                                                                    {{ $manager->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="line_status">Line Status</label>
                                                        <select class="form-control" name="line_status" required>
                                                            <option value="available" {{ $line->line_status == 'available' ? 'selected' : '' }}>Available</option>
                                                            <option value="in production" {{ $line->line_status == 'in production' ? 'selected' : '' }}>In Production</option>
                                                            <option value="offline" {{ $line->line_status == 'offline' ? 'selected' : '' }}>Offline</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Production Line Modal -->
                                <div class="modal fade" id="deleteProductionLineModal{{ $line->id }}" tabindex="-1" aria-labelledby="deleteProductionLineModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteProductionLineModalLabel">Delete Production Line</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this production line: <b>{{ $line->line_name }}</b>?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.deleteProductionLine', $line->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $productionLines->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Production Line Modal -->
<div class="modal fade" id="addProductionLineModal" tabindex="-1" aria-labelledby="addProductionLineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductionLineModalLabel">Add New Production Line</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.addProductionLine') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="line_name">Line Name</label>
                        <input type="text" class="form-control" name="line_name" required>
                    </div>
                    <div class="form-group">
                        <label for="max_quantity">Max Quantity</label>
                        <input type="number" class="form-control" name="max_quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="line_manager_id">Line Manager</label>
                        <select class="form-control" name="line_manager_id" required>
                            @foreach($lineManagers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="line_status">Line Status</label>
                        <select class="form-control" name="line_status" required>
                            <option value="available">Available</option>
                            <option value="in production">In Production</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Production Line</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('productionLineSearchInput');
        const table = document.getElementById('productionLineTable');
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
