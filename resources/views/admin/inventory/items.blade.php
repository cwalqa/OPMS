@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3">Inventory Items</h6>
                        <button class="btn btn-light btn-sm me-3" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            Add New Item
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <!-- Search Input -->
                        <input type="text" id="itemSearchInput" placeholder="Search Items..." class="form-control mb-3">
                        
                        <!-- Inventory Table -->
                        <table class="table align-items-center mb-0" id="itemTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Brand</th>
                                    <!-- <th>SKU</th> -->
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Sale Price</th>
                                    <!-- <th>Purchase Price</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td><a href="{{ route('admin.inventory.items.show', $item->id) }}">{{ $item->name }}</a></td>
                                        <td>{{ $item->description ?? 'N/A' }}</td>
                                        <td>{{ $item->brand->name ?? 'N/A' }}</td>
                                        <!-- <td>{{ $item->sku }}</td> -->
                                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                                        <td>{{ $item->stock }}</td>
                                        <td>&#36;{{ number_format($item->sale_price, 2) }}</td>
                                        <!-- <td>&#36;{{ number_format($item->purchase_price, 2) }}</td> -->
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.inventory.items.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Item Modal -->
                                    <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('admin.inventory.items.update', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Item Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea name="description" class="form-control">{{ $item->description }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="brand_id" class="form-label">Brand</label>
                                                            <select name="brand_id" class="form-control">
                                                                <option value="">Select Brand</option>
                                                                @foreach($brands as $brand)
                                                                    <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                                                                        {{ $brand->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="sku" class="form-label">SKU</label>
                                                            <input type="text" name="sku" class="form-control" value="{{ $item->sku }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="category_id" class="form-label">Category</label>
                                                            <select name="category_id" class="form-control">
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="stock" class="form-label">Stock</label>
                                                            <input type="number" name="stock" class="form-control" value="{{ $item->stock }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="sale_price" class="form-label">Sale Price</label>
                                                            <input type="text" name="sale_price" class="form-control" value="{{ $item->sale_price }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="purchase_price" class="form-label">Purchase Price</label>
                                                            <input type="text" name="purchase_price" class="form-control" value="{{ $item->purchase_price }}" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Update Item</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Edit Modal -->
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.inventory.items.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select name="brand_id" class="form-control">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Sale Price</label>
                        <input type="text" name="sale_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="purchase_price" class="form-label">Purchase Price</label>
                        <input type="text" name="purchase_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
    <label for="default_warehouse_id" class="form-label">Warehouse</label>
    <select name="default_warehouse_id" id="warehouseSelect" class="form-control" required>
        <option value="">Select Warehouse</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" data-lots="{{ $warehouse->lots }}">
                {{ $warehouse->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3" id="lotContainer" style="display: none;"> 
    <label for="lot_shelf" class="form-label">Lot/Shelf (Auto-Assigned)</label>
    <input type="text" name="lot_shelf" id="lotInput" class="form-control" readonly>
</div>


                    <button type="submit" class="btn btn-primary">Save Item</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript Loaded - Ready to execute scripts.");

    // Search functionality for filtering items
    const searchInput = document.getElementById("itemSearchInput");
    const tableRows = document.querySelectorAll("#itemTable tbody tr");

    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            const filter = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const textContent = row.innerText.toLowerCase();
                row.style.display = textContent.includes(filter) ? "" : "none";
            });
        });
    }

    const warehouseSelect = document.getElementById('warehouseSelect');
        const lotInput = document.getElementById('lotInput');
        const lotContainer = document.getElementById('lotContainer');

        if (!warehouseSelect || !lotInput || !lotContainer) {
            console.error("Dropdown elements not found!");
            return;
        }

        // Auto-assign a random lot when a warehouse is selected
        warehouseSelect.addEventListener('change', function () {
            const selectedWarehouse = warehouseSelect.options[warehouseSelect.selectedIndex];
            const lotsData = selectedWarehouse.getAttribute('data-lots');

            console.log("Warehouse Selected: ", selectedWarehouse.value);
            console.log("Lots Data: ", lotsData);

            if (lotsData) {
                const lotArray = lotsData.split(',').map(lot => lot.trim()); // Trim spaces
                console.log("Parsed Lots Array:", lotArray);

                if (lotArray.length > 0) {
                    const randomLot = lotArray[Math.floor(Math.random() * lotArray.length)]; // Pick a random lot
                    lotInput.value = randomLot; // Assign the random lot
                    lotContainer.style.display = "block"; // Show assigned lot field
                    console.log("Randomly Assigned Lot:", randomLot);
                } else {
                    lotInput.value = "";
                    lotContainer.style.display = "none"; // Hide if no lots available
                }
            } else {
                lotInput.value = "";
                lotContainer.style.display = "none"; // Hide if no lots are set
            }
        });

        // Reset fields when modal opens
        document.getElementById('addItemModal').addEventListener('shown.bs.modal', function () {
            console.log("Modal opened - Resetting fields");
            warehouseSelect.value = ''; // Reset warehouse selection
            lotInput.value = ''; // Reset auto-assigned lot
            lotContainer.style.display = "none"; // Hide lot input initially
        });
    });
</script>
@endsection
