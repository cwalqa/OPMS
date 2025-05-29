@extends('admin.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3 d-inline-block">SYSTEM ADMINISTRATORS</h6>
                        <button class="btn btn-light btn-sm me-3 float-end" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            Add New Admin
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <!-- Search bar -->
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search...">
                        
                        <table class="table align-items-center mb-0" id="adminsTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Admin ID</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Full Name</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Email</th>
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Roles</th>
                                    <!-- <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Permissions</th> -->
                                    <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $admin->id }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $admin->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $admin->email }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @foreach($admin->roles as $role)
                                                <span class="badge bg-info">{{ $role->name }}</span>
                                            @endforeach
                                        </p>
                                    </td>
                                    <!-- <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @foreach($admin->roles->pluck('permissions')->flatten() as $permission)
                                                <span class="badge bg-success">{{ $permission->name }}</span>
                                            @endforeach
                                        </p>
                                    </td> -->
                                    <td>
                                        <button class="btn btn-secondary btn-sm edit-admin-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAdminModal" 
                                                data-admin-id="{{ $admin->id }}" 
                                                data-admin-name="{{ $admin->name }}" 
                                                data-admin-email="{{ $admin->email }}" 
                                                data-role-id="{{ $admin->roles->pluck('id')->first() }}" 
                                                data-permissions="{{ $admin->roles->first()->permissions->pluck('id') }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAdminModal{{ $admin->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- Delete Admin Modal -->
                                <div class="modal fade" id="deleteAdminModal{{ $admin->id }}" tabindex="-1" aria-labelledby="deleteAdminModalLabel{{ $admin->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteAdminModalLabel{{ $admin->id }}">Delete Admin</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this admin?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.deleteAdmin', $admin->id) }}" method="POST">
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
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.addAdmin') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="roles" class="form-label">Roles</label>
                        <select multiple class="form-control" id="roles" name="roles[]">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <select multiple class="form-control" id="permissions" name="permissions[]">
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Admin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAdminForm" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Admin Details -->
                    <div class="form-group mb-3">
                        <label for="admin_name" class="form-label">Admin Name</label>
                        <input type="text" class="form-control" id="admin_name" name="name" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="email" readonly>
                    </div>

                    <!-- Password Management -->
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">New Password (Optional)</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password if you want to change it">
                    </div>

                    <!-- Roles -->
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" name="role_id" id="role">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Permissions (Multi-select) -->
                    <div class="form-group mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <select class="form-select" name="permissions[]" id="permissions" multiple>
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
        const editModal = document.getElementById('editAdminModal');

        // Attach event listener to all edit buttons
        document.querySelectorAll('.edit-admin-btn').forEach(button => {
            button.addEventListener('click', function () {
                const adminId = button.getAttribute('data-admin-id');
                const adminName = button.getAttribute('data-admin-name');
                const adminEmail = button.getAttribute('data-admin-email');
                const roleId = button.getAttribute('data-role-id');
                const permissions = button.getAttribute('data-permissions').split(',');

                // Set form action URL dynamically
                const form = document.getElementById('editAdminForm');
                form.action = `/admin/users/update/${adminId}`;

                // Set values in modal fields
                document.getElementById('admin_name').value = adminName;
                document.getElementById('admin_email').value = adminEmail;

                // Set role select value
                const roleSelect = document.getElementById('role');
                roleSelect.value = roleId;

                // Set permissions multi-select values
                const permissionSelect = document.getElementById('permissions');
                Array.from(permissionSelect.options).forEach(option => {
                    if (permissions.includes(option.value)) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                });
            });
        });

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#adminsTable tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const matches = Array.from(cells).some(cell => cell.innerText.toLowerCase().includes(filter));
                row.style.display = matches ? '' : 'none';
            });
        });
    });
</script>
