<div class="modal fade" id="modifyOrderModal" tabindex="-1" aria-labelledby="modifyOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Modify Your Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modifyOrderForm" action="{{ route('client.updateOrder', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Hidden container for tracking removed items -->
                    <div id="removedItemsContainer"></div>

                    <h6 class="mb-3">Current Order Items</h6>
                    <div class="table-responsive rounded shadow-sm">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark text-white">
                                <tr>
                                    <th>Product/Service</th>
                                    <th>Product ID</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody">
                                @foreach($order->items as $item)
                                    @php
                                        $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();
                                        $productName = $product ? $product->name : 'Unknown Product';
                                    @endphp
                                    <tr data-item-id="{{ $item->id }}">
                                        <td>
                                            <select name="items[{{ $item->id }}][product_id]" class="form-select product-select">
                                                @foreach ($items as $availableItem)
                                                    <option value="{{ $availableItem->item_id }}" 
                                                        data-rate="{{ $availableItem->unit_price }}" 
                                                        {{ $item->sku == $availableItem->item_id ? 'selected' : '' }}>
                                                        {{ $availableItem->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <!-- Hidden input fields to store pricing data -->
                                            <input type="hidden" name="items[{{ $item->id }}][unit_price]" class="item-unit-price" value="{{ $item->unit_price }}">
                                            <input type="hidden" name="items[{{ $item->id }}][total]" class="item-total" value="{{ $item->amount }}">
                                        </td>
                                        <td><input type="text" name="items[{{ $item->id }}][sku]" class="form-control" value="{{ $item->sku }}" readonly></td>
                                        <td><input type="number" name="items[{{ $item->id }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity }}" min="1"></td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm item-remove-btn" data-item-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Add New Items -->
                    <h6 class="mt-5 mb-3">Add New Items</h6>
                    <div id="newItemsContainer">
                        <div class="table-responsive rounded shadow-sm">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark text-white">
                                    <tr>
                                        <th>Product/Service</th>
                                        <th>Product ID</th>
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="newItemsBody">
                                    <!-- New items will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <button type="button" id="addNewItemBtn" class="btn btn-success btn-sm mt-3">
                        <i class="fas fa-plus-circle me-1"></i> Add Item
                    </button>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label for="customer_memo" class="form-label"><b>Additional Notes</b></label>
                        <textarea name="customer_memo" id="customer_memo" class="form-control" rows="3">{{ $order->customer_memo }}</textarea>
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
    // JavaScript for the Modify Order Modal
    document.addEventListener("DOMContentLoaded", function() {
        // First check if we're in the correct modal
        if (!document.getElementById("modifyOrderModal")) {
            console.log("Modify Order Modal not found on this page, exiting initialization");
            return;
        }
        // Cache DOM elements for the modify order modal
        const modifyOrderForm = document.getElementById("modifyOrderForm");
        const orderItemsBody = document.getElementById("orderItemsBody");
        const newItemsBody = document.getElementById("newItemsBody");
        const addNewItemBtn = document.getElementById("addNewItemBtn");
        const removedItemsContainer = document.getElementById("removedItemsContainer");
        
        // Counter for new items
        let newItemCounter = 0;
        
        // Initialize order modification functionality
        initOrderModification();
        
        function initOrderModification() {
            // Set up event listeners for quantity changes
            if (orderItemsBody) {
                orderItemsBody.addEventListener("input", function(e) {
                    if (e.target.classList.contains("item-quantity")) {
                        updateItemTotals();
                    }
                });
                
                // Set up event listeners for product selection changes
                orderItemsBody.addEventListener("change", function(e) {
                    if (e.target.classList.contains("product-select")) {
                        const row = e.target.closest("tr");
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        
                        if (selectedOption && selectedOption.value) {
                            // Update the SKU field
                            row.querySelector('input[name$="[sku]"]').value = selectedOption.value;
                            
                            // Update the hidden unit price field
                            const unitPrice = selectedOption.getAttribute("data-rate") || 0;
                            row.querySelector(".item-unit-price").value = unitPrice;
                            
                            // Update the total
                            updateItemTotals();
                        }
                    }
                });
            }
            
            // Set up event delegation for remove buttons inside the modal ONLY - FIX: Use more specific event delegation
            document.getElementById('modifyOrderModal').addEventListener('click', function(e) {
                if (e.target && (e.target.classList.contains('item-remove-btn') || 
                                (e.target.parentElement && e.target.parentElement.classList.contains('item-remove-btn')))) {
                    // Get the actual button (might be the icon inside the button that was clicked)
                    const removeBtn = e.target.classList.contains('item-remove-btn') ? 
                                    e.target : e.target.parentElement;
                    handleItemRemoval(removeBtn);
                    
                    // Prevent the event from bubbling up to document level handlers
                    e.stopPropagation();
                }
            });
            
            // Add event listener for the add new item button
            if (addNewItemBtn) {
                addNewItemBtn.addEventListener("click", addNewItem);
            }
            
            // Add event listener for the form submission
            if (modifyOrderForm) {
                modifyOrderForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    // Validate the form
                    if (validateModifyForm()) {
                        // Confirm submission
                        Swal.fire({
                            title: 'Save Changes?',
                            text: 'Please confirm that you want to update this order.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Update Order',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Updating your order',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                
                                // Submit the form
                                this.submit();
                            }
                        });
                    }
                });
            }
            
            // Initialize totals
            updateItemTotals();
        }
        
        function updateItemTotals() {
            // Calculate totals for existing items
            if (orderItemsBody) {
                const rows = orderItemsBody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const unitPriceInput = row.querySelector('.item-unit-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const totalInput = row.querySelector('.item-total');
                    
                    if (unitPriceInput && quantityInput && totalInput) {
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;
                        const quantity = parseInt(quantityInput.value) || 0;
                        const total = unitPrice * quantity;
                        
                        totalInput.value = total.toFixed(2);
                    }
                });
            }
            
            // Calculate totals for new items
            if (newItemsBody) {
                const rows = newItemsBody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const unitPriceInput = row.querySelector('.item-unit-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const totalInput = row.querySelector('.item-total');
                    
                    if (unitPriceInput && quantityInput && totalInput) {
                        const unitPrice = parseFloat(unitPriceInput.value) || 0;
                        const quantity = parseInt(quantityInput.value) || 0;
                        const total = unitPrice * quantity;
                        
                        totalInput.value = total.toFixed(2);
                    }
                });
            }
        }
        
        function handleItemRemoval(removeBtn) {
            console.log('Remove button clicked:', removeBtn);
            const row = removeBtn.closest('tr');
            const itemId = removeBtn.getAttribute('data-item-id');
            
            console.log('Row:', row, 'Item ID:', itemId);
            
            if (!row || !itemId) {
                console.error('Could not find row or item ID');
                return;
            }
            
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item from your order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove It',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Confirmation approved, removing item:', itemId);
                    
                    // Check if it's an existing item
                    const isExistingItem = row.parentNode && row.parentNode.id === 'orderItemsBody';
                    console.log('Is existing item:', isExistingItem);
                    
                    if (isExistingItem) {
                        // For existing items, add a hidden input to track removal
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'removed_items[]';
                        hiddenInput.value = itemId;
                        removedItemsContainer.appendChild(hiddenInput);
                        console.log('Added hidden input for removed item:', hiddenInput);
                    }
                    
                    // Remove the row from the table
                    row.remove();
                    console.log('Row removed from DOM');
                    
                    // Update totals
                    updateItemTotals();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Item removed from order');
                    } else {
                        console.log('Toastr not available, showing alert instead');
                        alert('Item removed from order');
                    }
                }
            }).catch(error => {
                console.error('Error in SweetAlert:', error);
            });
        }

        
        function addNewItem() {
            const newItemId = `new_${newItemCounter++}`;
            const productOptions = Array.from(document.querySelectorAll('#orderItemsBody .product-select option'))
                .map(option => {
                    return {
                        value: option.value,
                        text: option.text,
                        rate: option.getAttribute('data-rate') || 0
                    };
                })
                .filter((item, index, self) => {
                    // Filter out duplicates based on value
                    return self.findIndex(t => t.value === item.value) === index;
                });
            
            // Create options HTML
            let optionsHtml = '<option value="">Select a product</option>';
            productOptions.forEach(option => {
                optionsHtml += `<option value="${option.value}" data-rate="${option.rate}">${option.text}</option>`;
            });
            
            // Create new item row
            const newRow = document.createElement('tr');
            newRow.dataset.itemId = newItemId;
            newRow.innerHTML = `
                <td>
                    <select name="new_items[${newItemId}][product_id]" class="form-select product-select">
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="new_items[${newItemId}][unit_price]" class="item-unit-price" value="0">
                    <input type="hidden" name="new_items[${newItemId}][total]" class="item-total" value="0">
                </td>
                <td><input type="text" name="new_items[${newItemId}][sku]" class="form-control" readonly></td>
                <td><input type="number" name="new_items[${newItemId}][quantity]" class="form-control item-quantity" value="1" min="1"></td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm item-remove-btn" data-item-id="${newItemId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            // Add the new row to the table
            newItemsBody.appendChild(newRow);
            
            // Add event listeners to the new row
            const productSelect = newRow.querySelector('.product-select');
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption && selectedOption.value) {
                    // Update the SKU field
                    newRow.querySelector('input[name$="[sku]"]').value = selectedOption.value;
                    
                    // Update the hidden unit price field
                    const unitPrice = selectedOption.getAttribute('data-rate') || 0;
                    newRow.querySelector('.item-unit-price').value = unitPrice;
                    
                    // Update the total
                    updateItemTotals();
                }
            });
            
            // Add event listener for quantity changes
            const quantityInput = newRow.querySelector('.item-quantity');
            quantityInput.addEventListener('input', updateItemTotals);
            
            // Scroll to the new item
            newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus on the product dropdown
            productSelect.focus();
            
            // Initialize select2 for this element if available
            try {
                $(productSelect).select2({
                    dropdownParent: $('#modifyOrderModal'),
                    placeholder: 'Select a product',
                    width: '100%'
                });
            } catch (e) {
                // Select2 might not be available
                console.log('Select2 initialization failed or not available');
            }
        }
        
        function validateModifyForm() {
            let isValid = true;
            let errorMessage = '';
            
            // Check if there are any items left after removal
            const existingItems = orderItemsBody.querySelectorAll('tr');
            const newItems = newItemsBody.querySelectorAll('tr');
            const removedItems = removedItemsContainer.querySelectorAll('input');
            
            if (existingItems.length - removedItems.length + newItems.length === 0) {
                isValid = false;
                errorMessage = 'Your order must contain at least one item.';
            }
            
            // Validate new items
            newItems.forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantity = row.querySelector('.item-quantity');
                
                if (!productSelect.value) {
                    isValid = false;
                    errorMessage = 'Please select a product for all new items.';
                    productSelect.classList.add('is-invalid');
                } else {
                    productSelect.classList.remove('is-invalid');
                }
                
                if (!quantity.value || parseInt(quantity.value) < 1) {
                    isValid = false;
                    errorMessage = 'Quantity must be at least 1 for all items.';
                    quantity.classList.add('is-invalid');
                } else {
                    quantity.classList.remove('is-invalid');
                }
            });
            
            // Validate existing items
            existingItems.forEach(row => {
                const quantity = row.querySelector('.item-quantity');
                
                if (!quantity.value || parseInt(quantity.value) < 1) {
                    isValid = false;
                    errorMessage = 'Quantity must be at least 1 for all items.';
                    quantity.classList.add('is-invalid');
                } else {
                    quantity.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                Swal.fire({
                    title: 'Form Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            
            return isValid;
        }
        
        // Add listener for the modal being shown - refresh data when modal opens
        const modifyOrderModal = document.getElementById('modifyOrderModal');
        if (modifyOrderModal) {
            modifyOrderModal.addEventListener('shown.bs.modal', function() {
                // Reset new items when modal is opened
                if (newItemsBody) {
                    newItemsBody.innerHTML = '';
                    newItemCounter = 0;
                }
                
                // Reset removed items container
                if (removedItemsContainer) {
                    removedItemsContainer.innerHTML = '';
                }
                
                // Re-initialize the dropdown selects if using a plugin like Select2
                try {
                    $('.product-select').select2({
                        dropdownParent: $('#modifyOrderModal'),
                        placeholder: 'Select a product',
                        width: '100%'
                    });
                } catch (e) {
                    // Select2 might not be available, so we catch any errors
                    console.log('Select2 initialization failed or not available');
                }
                
                // Update totals
                updateItemTotals();
            });
        }
    });
</script>
@endpush