@extends('admin.app')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">CLIENTS & COMPANIES</h6>
                        <button class="btn btn-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            Add New Customer
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                    <!-- Search Bar -->
                   
                        <input type="text" id="customerSearchInput" class="form-control mb-3" placeholder="Search...">
                    
                        <div class="table-responsive p-3">
                            <table class="table align-items-center mb-0" id="customerTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Customer ID</th>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Full Name</th>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Company Name</th>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Email</th>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10 ps-2">Status</th>
                                        <th class="text-uppercase text-primary text-sm font-weight-bolder opacity-10">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->customer_id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->fully_qualified_name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->company_name ?? 'N/A' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $customer->email }}</p>
                                        </td>
                                        <td>
                                            @if($customer->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm view-customer-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewCustomerModal" 
                                                    data-customer-id="{{ $customer->customer_id }}"
                                                    data-customer-name="{{ $customer->fully_qualified_name }}"
                                                    data-company-name="{{ $customer->company_name }}"
                                                    data-email="{{ $customer->email }}"
                                                    data-status="{{ $customer->is_active ? 'Active' : 'Inactive' }}">
                                                View
                                            </button>
                                            <button class="btn btn-primary btn-sm edit-customer-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCustomerModal" 
                                                    data-customer-id="{{ $customer->customer_id }}"
                                                    data-customer-name="{{ $customer->fully_qualified_name }}"
                                                    data-company-name="{{ $customer->company_name }}"
                                                    data-email="{{ $customer->email }}"
                                                    data-status="{{ $customer->is_active }}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Customer ID:</strong> <span id="viewCustomerId"></span></p>
                <p><strong>Full Name:</strong> <span id="viewCustomerName"></span></p>
                <p><strong>Company Name:</strong> <span id="viewCompanyName"></span></p>
                <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editCustomerId" name="customer_id">

                    <div class="mb-3">
                        <label for="editCustomerName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editCustomerName" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCompanyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="editCompanyName" name="company_name">
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
        const searchInput = document.getElementById('customerSearchInput');
        const table = document.getElementById('customerTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) {
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

        // Populate view modal with customer data
        document.querySelectorAll('.view-customer-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('viewCustomerId').textContent = button.getAttribute('data-customer-id');
                document.getElementById('viewCustomerName').textContent = button.getAttribute('data-customer-name');
                document.getElementById('viewCompanyName').textContent = button.getAttribute('data-company-name');
                document.getElementById('viewEmail').textContent = button.getAttribute('data-email');
                document.getElementById('viewStatus').textContent = button.getAttribute('data-status');
            });
        });

        // Populate edit modal with customer data
        document.querySelectorAll('.edit-customer-btn').forEach(button => {
    button.addEventListener('click', function () {
        const customerId = button.getAttribute('data-customer-id');
        document.getElementById('editCustomerForm').action = `/admin/users/customers/${customerId}`;
        
        // Fill in form fields
        document.getElementById('editCustomerId').value = customerId;
        document.getElementById('editCustomerName').value = button.getAttribute('data-customer-name');
        document.getElementById('editCompanyName').value = button.getAttribute('data-company-name');
        document.getElementById('editEmail').value = button.getAttribute('data-email');
        document.getElementById('editStatus').value = button.getAttribute('data-status');
    });
});

    });
</script>
