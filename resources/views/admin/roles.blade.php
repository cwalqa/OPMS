@extends('admin.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Roles Management</h6>
                        <button class="btn btn-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            Add New Role
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search Roles...">
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0" id="rolesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Role Name</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Permissions</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $role->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @foreach($role->permissions as $permission)
                                                <span class="badge bg-success">{{ $permission->name }}</span>
                                            @endforeach
                                        </p>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-secondary btn-sm edit-role-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editRoleModal"
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}"
                                                data-permissions="{{ $role->permissions->pluck('id') }}">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- Delete Role Modal -->
                                <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteRoleModalLabel{{ $role->id }}">Delete Role</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this role?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST">
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
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="role_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <select multiple class="form-control" id="permissions" name="permissions[]">
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Role</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Role Details -->
                    <div class="form-group mb-3">
                        <label for="role_name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="role_name_edit" name="name" required>
                    </div>

                    <!-- Permissions (Multi-select) -->
                    <div class="form-group mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <select class="form-select" name="permissions[]" id="permissions_edit" multiple>
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Search functionality for the roles table
        const searchInput = document.getElementById('searchInput');
        const rolesTable = document.getElementById('rolesTable');
        const rows = rolesTable.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        });

        // Edit role modal handling
        const editModal = document.getElementById('editRoleModal');
        document.querySelectorAll('.edit-role-btn').forEach(button => {
            button.addEventListener('click', function () {
                const roleId = button.getAttribute('data-role-id');
                const roleName = button.getAttribute('data-role-name');
                const permissions = button.getAttribute('data-permissions').split(',');

                // Set form action URL dynamically
                const form = document.getElementById('editRoleForm');
                form.action = `/admin/roles/${roleId}`;

                // Set values in modal fields
                document.getElementById('role_name_edit').value = roleName;

                // Set permissions multi-select values
                const permissionSelect = document.getElementById('permissions_edit');
                Array.from(permissionSelect.options).forEach(option => {
                    if (permissions.includes(option.value)) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                });
            });
        });
    });
</script>
