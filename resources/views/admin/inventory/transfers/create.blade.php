@extends('admin.app')

@section('title', 'Create Transfer')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0">Create New Transfer</h5>
                    <a href="{{ route('inventory.transfers') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Transfers
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('inventory.transfers.store') }}" method="POST">
                        @csrf

                        <!-- Select Item -->
                        <div class="mb-3">
                            <label for="item_id" class="form-label">Item</label>
                            <select name="item_id" id="item_id" class="form-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" 
                                            data-default-warehouse="{{ $item->default_warehouse_id }}" 
                                            data-stock="{{ $item->stock }}">
                                        {{ $item->name }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Source Warehouse (Readonly) -->
                        <div class="mb-3">
                            <label for="source_warehouse" class="form-label">From Warehouse</label>
                            <input type="text" id="source_warehouse" class="form-control" readonly>
                            <input type="hidden" name="source_warehouse_id" id="source_warehouse_id">
                        </div>

                        <!-- Source Lot/Shelf (Readonly) -->
                        <div class="mb-3">
                            <label for="source_lot_shelf" class="form-label">From Shelf/Lot</label>
                            <input type="text" id="source_lot_shelf" class="form-control" readonly>
                            <input type="hidden" name="source_lot_shelf" id="hidden_source_lot_shelf">
                        </div>

                        <!-- Destination Warehouse -->
                        <div class="mb-3">
                            <label for="destination_warehouse_id" class="form-label">To Warehouse</label>
                            <select name="destination_warehouse_id" id="destination_warehouse_id" class="form-select" required>
                                <option value="">Select Destination Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" data-lots="{{ $warehouse->lots }}">
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Destination Lot/Shelf -->
                        <div class="mb-3">
                            <label for="destination_lot_shelf" class="form-label">To Shelf/Lot</label>
                            <select name="destination_lot_shelf" id="destination_lot_shelf" class="form-select">
                                <option value="">Select Destination Shelf/Lot</option>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                            <small id="available_quantity" class="form-text text-muted"></small>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional transfer notes..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Execute Transfer</button>
                            <a href="{{ route('inventory.transfers') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('item_id');
        const sourceWarehouseInput = document.getElementById('source_warehouse');
        const sourceWarehouseHidden = document.getElementById('source_warehouse_id');
        const sourceShelfInput = document.getElementById('source_lot_shelf');
        const sourceShelfHidden = document.getElementById('hidden_source_lot_shelf');
        const destinationWarehouseSelect = document.getElementById('destination_warehouse_id');
        const destinationShelfSelect = document.getElementById('destination_lot_shelf');
        const quantityInput = document.getElementById('quantity');
        const availableQuantityLabel = document.getElementById('available_quantity');

        // Fetch Warehouse & Lot when Item is Selected
        itemSelect.addEventListener('change', function () {
            const itemId = this.value;
            if (!itemId) {
                resetFields();
                return;
            }

            // Get Default Warehouse ID
            const defaultWarehouseId = this.options[this.selectedIndex].dataset.defaultWarehouse;
            const selectedStock = this.options[this.selectedIndex].dataset.stock || 0;

            // Fetch Source Warehouse & Lot Details from API
            fetch(`/admin/inventory/items/${itemId}/warehouse-lot`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.warehouse) {
                        sourceWarehouseInput.value = data.warehouse.name;
                        sourceWarehouseHidden.value = data.warehouse.id;
                        sourceShelfInput.value = data.lot_shelf || "Default";
                        sourceShelfHidden.value = data.lot_shelf || "Default";
                        quantityInput.max = selectedStock;
                        availableQuantityLabel.textContent = `Available stock: ${selectedStock}`;
                        quantityInput.value = selectedStock;
                    }
                })
                .catch(error => console.error('Error fetching warehouse details:', error));
        });

        // Populate Destination Lots when Warehouse is Selected
        destinationWarehouseSelect.addEventListener('change', function () {
            const selectedWarehouse = this.options[this.selectedIndex];
            const lots = selectedWarehouse.getAttribute('data-lots');
            destinationShelfSelect.innerHTML = '<option value="">Select Destination Shelf/Lot</option>';

            if (lots) {
                const lotArray = lots.split(',').map(lot => lot.trim());
                lotArray.forEach(lot => {
                    if (lot) {
                        const option = document.createElement('option');
                        option.value = lot;
                        option.textContent = lot;
                        destinationShelfSelect.appendChild(option);
                    }
                });
            }
        });

        // Ensure Quantity does not Exceed Available Stock
        quantityInput.addEventListener('input', function () {
            const max = parseInt(this.max);
            if (parseInt(this.value) > max) this.value = max;
            if (parseInt(this.value) < 1) this.value = 1;
        });

        // Reset Fields
        function resetFields() {
            sourceWarehouseInput.value = "";
            sourceWarehouseHidden.value = "";
            sourceShelfInput.value = "";
            sourceShelfHidden.value = "";
            quantityInput.value = "";
            quantityInput.max = "";
            availableQuantityLabel.textContent = "";
        }
    });
</script>
@endpush
@endsection
