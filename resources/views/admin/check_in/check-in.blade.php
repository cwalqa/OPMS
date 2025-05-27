@extends('admin.app')

@php
    use App\Models\WarehouseItem;
@endphp

@section('content')
<div class="container py-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-gradient-primary text-white rounded-top-4">
            <h5 class="mb-0">
                <i class="fas fa-warehouse me-2"></i> Check-In for PO #{{ $estimate->purchase_order_number }}
            </h5>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Client: {{ $estimate->customer_name }}</h6>
                    <p class="mb-1"><strong>PO Number:</strong> {{ $estimate->purchase_order_number }}</p>
                    <p class="mb-1"><strong>Client PO:</strong> {{ $estimate->client_po_number }}</p>
                    <p class="mb-1"><strong>Status:</strong> 
                        <span class="badge {{ $estimate->status === 'checked_in' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($estimate->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.check_in.preview', $estimate->id) }}" id="checkInForm">
                @csrf

                <table class="table table-sm table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Warehouse</th>
                            <th>Lot (optional)</th>
                            <th>Shelf (optional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estimate->items as $item)
                            @php
                                $isFullyChecked = $item->check_in_status === 'checked_in';
                                $rowClass = $isFullyChecked ? 'table-success' : '';
                                $existingItems = WarehouseItem::where('estimate_item_id', $item->id)->count();
                                $remaining = $item->quantity - $existingItems;
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $remaining }} of {{ $item->quantity }}</td>
                                <td>
                                    <select name="items[{{ $item->id }}][warehouse_id]" 
                                            class="form-select form-select-sm warehouse-select" 
                                            data-item="{{ $item->id }}"
                                            {{ $isFullyChecked || $remaining <= 0 ? 'disabled' : '' }} required>
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old("items.{$item->id}.warehouse_id") == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{ $item->id }}][lot]" 
                                            class="form-select form-select-sm lot-select"
                                            data-item="{{ $item->id }}"
                                            {{ $isFullyChecked || $remaining <= 0 ? 'disabled' : '' }}>
                                        <option value="">Select Lot (optional)</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{ $item->id }}][shelf]" 
                                            class="form-select form-select-sm shelf-select"
                                            data-item="{{ $item->id }}"
                                            {{ $isFullyChecked || $remaining <= 0 ? 'disabled' : '' }}>
                                        <option value="">Select Shelf (optional)</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No estimate items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="alert alert-light small">
                    <i class="fas fa-info-circle me-1"></i>
                    Items already marked as <strong>checked in</strong> are disabled.
                    Only pending ones will be processed. <strong>Lots and Shelves are now optional</strong> - you can proceed with just a warehouse selected.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6>Please correct the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.check_in.start') }}" class="btn btn-secondary">
                        <i class="fas fa-chevron-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Preview Labels
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Debug Information -->
<div class="mt-3" style="display: none;" id="debugInfo">
    <div class="card">
        <div class="card-header">
            <button class="btn btn-sm btn-outline-secondary" type="button" onclick="toggleDebug()">
                Toggle Debug Info
            </button>
            <button class="btn btn-sm btn-outline-info ms-2" type="button" onclick="testDropdowns()">
                Test Dropdowns
            </button>
        </div>
        <div class="card-body" id="debugContent" style="display: none;">
            <h6>Warehouse Structure:</h6>
            <pre id="debugJson"></pre>
            <div id="testResults" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const warehouses = @json($warehouseStructure);
    console.log('🏗️ Warehouse structure loaded:', warehouses);
    
    // Show debug info in development
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('local')) {
        document.getElementById('debugInfo').style.display = 'block';
        document.getElementById('debugJson').textContent = JSON.stringify(warehouses, null, 2);
    }

    // Store old values for form repopulation
    const oldValues = @json(old('items', []));
    console.log('📝 Old form values:', oldValues);

    // Show server-side validation errors with SweetAlert
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: `
                <div class="text-start">
                    <p>Please correct the following errors:</p>
                    <ul class="text-start ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            customClass: {
                popup: 'swal-wide'
            }
        });
    @endif

    // UPDATED: Simplified form validation - only require warehouse selection
    const form = document.getElementById('checkInForm');
    form.addEventListener('submit', function(event) {
        let valid = true;
        let errorMessages = [];
        
        document.querySelectorAll('.warehouse-select').forEach(warehouseSelect => {
            if (warehouseSelect.disabled) return;
            
            const itemId = warehouseSelect.dataset.item;
            
            // Only check if warehouse is selected - lots and shelves are now optional
            if (!warehouseSelect.value) {
                valid = false;
                warehouseSelect.classList.add('is-invalid');
                errorMessages.push(`Please select a warehouse for item ${itemId}`);
            } else {
                warehouseSelect.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            event.preventDefault();
            
            // Show SweetAlert with validation errors
            Swal.fire({
                icon: 'error',
                title: 'Form Validation Failed',
                html: `
                    <div class="text-start">
                        <p>Please correct the following issues:</p>
                        <ul class="text-start ps-3">
                            ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                        </ul>
                    </div>
                `,
                customClass: {
                    popup: 'swal-wide'
                },
                showConfirmButton: true,
                confirmButtonText: 'Got it!',
                confirmButtonColor: '#dc3545'
            });
        }
    });

    // Function to populate lots and shelves
    function populateLotsAndShelves(warehouseId, itemId, selectedLot = '', selectedShelf = '') {
        console.log(`🔄 Populating lots/shelves for warehouse ${warehouseId}, item ${itemId}`);
        
        const lotSelect = document.querySelector(`.lot-select[data-item="${itemId}"]`);
        const shelfSelect = document.querySelector(`.shelf-select[data-item="${itemId}"]`);

        if (!lotSelect || !shelfSelect) {
            console.error('❌ Could not find lot or shelf select elements');
            return;
        }

        // Reset the selects
        lotSelect.innerHTML = '<option value="">Select Lot (optional)</option>';
        shelfSelect.innerHTML = '<option value="">Select Shelf (optional)</option>';

        if (!warehouseId) {
            console.log('⚠️ No warehouse selected');
            return;
        }

        // Find the selected warehouse in the pre-loaded data
        const selectedWarehouse = warehouses.find(w => w.id == warehouseId);
        console.log('🏢 Found warehouse in pre-loaded data:', selectedWarehouse);
        console.log('🔍 Looking for warehouse ID:', warehouseId, 'Type:', typeof warehouseId);
        
        if (selectedWarehouse) {
            console.log('✅ Using pre-loaded data');
            console.log('📦 Lots available:', selectedWarehouse.lots);
            console.log('📚 Shelves available:', selectedWarehouse.shelves);
            
            // Populate lots from pre-loaded data
            if (selectedWarehouse.lots && selectedWarehouse.lots.length > 0) {
                selectedWarehouse.lots.forEach(lot => {
                    const option = document.createElement('option');
                    option.value = lot.code;
                    option.textContent = lot.code;
                    if (lot.code === selectedLot) {
                        option.selected = true;
                    }
                    lotSelect.appendChild(option);
                });
                console.log(`✅ Added ${selectedWarehouse.lots.length} lots`);
            } else {
                console.log('⚠️ No lots available for this warehouse');
            }
            
            // Populate shelves from pre-loaded data
            if (selectedWarehouse.shelves && selectedWarehouse.shelves.length > 0) {
                selectedWarehouse.shelves.forEach(shelf => {
                    const option = document.createElement('option');
                    option.value = shelf.code;
                    option.textContent = shelf.code;
                    if (shelf.code === selectedShelf) {
                        option.selected = true;
                    }
                    shelfSelect.appendChild(option);
                });
                console.log(`✅ Added ${selectedWarehouse.shelves.length} shelves`);
            } else {
                console.log('⚠️ No shelves available for this warehouse');
            }
        } else {
            console.log('❌ Warehouse not found in pre-loaded data, falling back to AJAX calls');
            
            // Fallback to AJAX calls if warehouse is not found in pre-loaded data
            const lotsUrl = `/admin/check-in/warehouse/${warehouseId}/lots`;
            const shelvesUrl = `/admin/check-in/warehouse/${warehouseId}/shelves`;
            
            console.log('🌐 Fetching lots from:', lotsUrl);
            console.log('🌐 Fetching shelves from:', shelvesUrl);
            
            // Fetch lots
            fetch(lotsUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    console.log('📡 Lots response:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Lots data received:', data);
                    if (Array.isArray(data)) {
                        data.forEach(lot => {
                            const option = document.createElement('option');
                            option.value = lot.code;
                            option.textContent = lot.code;
                            if (lot.code === selectedLot) {
                                option.selected = true;
                            }
                            lotSelect.appendChild(option);
                        });
                        console.log(`✅ Added ${data.length} lots via AJAX`);
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching lots:', error);
                    // Show toast notification but don't block the process
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lots Loading Issue',
                        text: 'Could not load lots for this warehouse, but you can proceed without selecting a lot.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            
            // Fetch shelves
            fetch(shelvesUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    console.log('📡 Shelves response:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📚 Shelves data received:', data);
                    if (Array.isArray(data)) {
                        data.forEach(shelf => {
                            const option = document.createElement('option');
                            option.value = shelf.code;
                            option.textContent = shelf.code;
                            if (shelf.code === selectedShelf) {
                                option.selected = true;
                            }
                            shelfSelect.appendChild(option);
                        });
                        console.log(`✅ Added ${data.length} shelves via AJAX`);
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching shelves:', error);
                    // Show toast notification but don't block the process
                    Swal.fire({
                        icon: 'warning',
                        title: 'Shelves Loading Issue',
                        text: 'Could not load shelves for this warehouse, but you can proceed without selecting a shelf.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
        }
    }

    // Set up event listeners for warehouse select changes
    document.querySelectorAll('.warehouse-select').forEach(warehouseSelect => {
        warehouseSelect.addEventListener('change', function () {
            const itemId = this.dataset.item;
            const warehouseId = this.value;

            console.log(`🏢 Warehouse selected: ${warehouseId} for item: ${itemId}`);
            
            populateLotsAndShelves(warehouseId, itemId);
            
            // Remove validation error styling when warehouse changes
            this.classList.remove('is-invalid');
        });
    });

    // Initialize lots and shelves for already selected warehouses (including old values)
    document.querySelectorAll('.warehouse-select').forEach(warehouseSelect => {
        const itemId = warehouseSelect.dataset.item;
        const currentWarehouseId = warehouseSelect.value;
        
        if (currentWarehouseId) {
            console.log(`🔄 Initializing lots/shelves for item ${itemId} with warehouse ${currentWarehouseId}`);
            
            // Check if we have old values to restore
            if (oldValues[itemId]) {
                populateLotsAndShelves(
                    currentWarehouseId, 
                    itemId, 
                    oldValues[itemId].lot || '', 
                    oldValues[itemId].shelf || ''
                );
            } else {
                populateLotsAndShelves(currentWarehouseId, itemId);
            }
        }
    });
});

function toggleDebug() {
    const debugContent = document.getElementById('debugContent');
    debugContent.style.display = debugContent.style.display === 'none' ? 'block' : 'none';
}

function testDropdowns() {
    const warehouses = @json($warehouseStructure);
    const testResults = document.getElementById('testResults');
    
    let html = '<h6>Dropdown Test Results:</h6>';
    
    // Test each warehouse select
    document.querySelectorAll('.warehouse-select').forEach((select, index) => {
        const itemId = select.dataset.item;
        const selectedValue = select.value;
        
        html += `<div class="alert alert-light small mb-2">
            <strong>Item ${itemId}:</strong><br>
            - Warehouse Select Value: "${selectedValue}" (${typeof selectedValue})<br>
            - Available warehouses: ${warehouses.map(w => `${w.name} (ID: ${w.id})`).join(', ')}<br>
        `;
        
        if (selectedValue) {
            const warehouse = warehouses.find(w => w.id == selectedValue);
            if (warehouse) {
                html += `- Found warehouse: ${warehouse.name}<br>`;
                html += `- Lots: ${warehouse.lots.length > 0 ? warehouse.lots.map(l => l.code).join(', ') : 'None'}<br>`;
                html += `- Shelves: ${warehouse.shelves.length > 0 ? warehouse.shelves.map(s => s.code).join(', ') : 'None'}<br>`;
                
                // Test populating dropdowns
                const lotSelect = document.querySelector(`.lot-select[data-item="${itemId}"]`);
                const shelfSelect = document.querySelector(`.shelf-select[data-item="${itemId}"]`);
                
                html += `- Lot options count: ${lotSelect.options.length}<br>`;
                html += `- Shelf options count: ${shelfSelect.options.length}<br>`;
            } else {
                html += `- <span class="text-danger">Warehouse not found in pre-loaded data!</span><br>`;
            }
        } else {
            html += `- No warehouse selected<br>`;
        }
        
        html += '</div>';
    });
    
    testResults.innerHTML = html;
}
</script>

<style>
/* Custom styles for SweetAlert */
.swal-wide {
    width: 600px !important;
}

.swal2-html-container ul {
    margin: 0;
    padding-left: 1.5rem;
}

.swal2-html-container li {
    text-align: left;
    margin-bottom: 0.25rem;
}
</style>
@endsection