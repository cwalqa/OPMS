<div class="modal fade" id="modifyOrderModal" tabindex="-1" aria-labelledby="modifyOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content shadow border-0 rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Modify Your Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modifyOrderForm" action="{{ route('client.updateOrder', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div id="removedItemsContainer"></div>

                    <h6 class="mb-3">Order Items</h6>
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Product/Service</th>
                                <th>Product ID</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsBody">
                            @foreach($order->items as $item)
                                @php
                                    $product = \App\Models\QuickbooksItem::where('item_id', $item->sku)->first();
                                @endphp
                                <tr data-item-id="{{ $item->id }}">
                                    <td>
                                        <select name="items[{{ $item->id }}][product_service]" class="form-select product-select">
                                            @foreach ($items as $availableItem)
                                                <option value="{{ $availableItem->item_id }}" data-rate="{{ $availableItem->unit_price }}" {{ $availableItem->item_id == $item->sku ? 'selected' : '' }}>
                                                    {{ $availableItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}">
                                    </td>
                                    <td><input type="text" name="items[{{ $item->id }}][ps_id]" class="form-control" value="{{ $item->sku }}" readonly></td>
                                    <td><input type="text" name="items[{{ $item->id }}][description]" class="form-control" value="{{ $item->description }}"></td>
                                    <td><input type="number" name="items[{{ $item->id }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity }}" min="1">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger item-remove-btn" data-item-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h6 class="mt-4">Add New Items</h6>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Product/Service</th>
                                <th>Product ID</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="newItemsBody"></tbody>
                    </table>

                    <button type="button" class="btn btn-success btn-sm" id="addNewItemBtn"><i class="fas fa-plus-circle"></i> Add Item</button>

                    <div class="mb-3 mt-4">
                        <label for="po_file" class="form-label fw-bold">Replace Attached PO Document</label>
                        <input type="file" name="po_file" id="po_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="customer_memo" class="form-control">{{ $order->customer_memo }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('modifyOrderForm');
    const addNewItemBtn = document.getElementById('addNewItemBtn');
    const removedItemsContainer = document.getElementById('removedItemsContainer');
    const orderItemsBody = document.getElementById('orderItemsBody');
    const newItemsBody = document.getElementById('newItemsBody');
    let newItemIndex = 0;

    console.log("Modify Order modal script initialized.");

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        console.log("Save button clicked");

        if (!validateForm()) {
            console.warn("Validation failed, form not submitted.");
            return;
        }

        Swal.fire({
            title: 'Save Changes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save',
        }).then(result => {
            if (result.isConfirmed) {
                console.log("Form passed validation and user confirmed submission.");
                form.submit();
            } else {
                console.log("User cancelled submission.");
            }
        });
    });

    function validateForm() {
        const rows = [...orderItemsBody.querySelectorAll('tr'), ...newItemsBody.querySelectorAll('tr')];
        let valid = true;
        let totalItems = 0;

        rows.forEach((row, index) => {
            const qty = row.querySelector('.item-quantity');
            const select = row.querySelector('.product-select');

            console.group(`Validating Row ${index + 1}`);

            if (!qty || !select) {
                console.warn("Missing expected input fields in row. Skipping this row.");
                console.groupEnd();
                return; // Skip this row if it doesn't have the required inputs
            }

            console.log("Quantity value:", qty.value);
            console.log("Selected product:", select.value);

            if (!qty.value || parseInt(qty.value) < 1) {
                qty.classList.add('is-invalid');
                console.warn("Invalid quantity detected.");
                valid = false;
            } else {
                qty.classList.remove('is-invalid');
            }

            if (!select.value) {
                select.classList.add('is-invalid');
                console.warn("Empty product selection detected.");
                valid = false;
            } else {
                select.classList.remove('is-invalid');
            }

            totalItems++;
            console.groupEnd();
        });

        if (totalItems === 0) {
            Swal.fire('Error', 'At least one item is required.', 'error');
            console.warn("No items in form.");
            return false;
        }

        console.log("Form validation result:", valid);
        return valid;
    }


    document.getElementById('modifyOrderModal').addEventListener('click', function (e) {
        const btn = e.target.closest('.item-remove-btn');
        if (btn) {
            const row = btn.closest('tr');
            const itemId = btn.getAttribute('data-item-id');
            const isExisting = row.parentElement.id === 'orderItemsBody';

            Swal.fire({
                title: 'Remove this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
            }).then(result => {
                if (result.isConfirmed) {
                    if (isExisting) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'removed_items[]';
                        hidden.value = itemId;
                        removedItemsContainer.appendChild(hidden);
                        console.log(`Marked item #${itemId} for removal`);
                    } else {
                        console.log(`Removing new unsaved item row: ${itemId}`);
                    }
                    row.remove();
                } else {
                    console.log("Item removal cancelled by user.");
                }
            });
        }
    });

    addNewItemBtn.addEventListener('click', () => {
        console.log("Add new item button clicked.");
        const options = document.querySelector('.product-select')?.innerHTML;

        if (!options) {
            console.error("No product options found.");
            return;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="new_items[new_${newItemIndex}][product_id]" class="form-select product-select">${options}</select>
                <input type="hidden" name="new_items[new_${newItemIndex}][unit_price]" class="item-unit-price" value="0">
                <input type="hidden" name="new_items[new_${newItemIndex}][total]" class="item-total" value="0">
            </td>
            <td><input type="text" name="new_items[new_${newItemIndex}][sku]" class="form-control" readonly></td>
            <td><input type="text" name="new_items[new_${newItemIndex}][description]" class="form-control" placeholder="Enter description"></td>
            <td><input type="number" name="new_items[new_${newItemIndex}][quantity]" class="form-control item-quantity" value="1" min="1"></td>
            <td><button type="button" class="btn btn-danger btn-sm item-remove-btn" data-item-id="new_${newItemIndex}"><i class="fas fa-trash"></i></button></td>
        `;


        newItemsBody.appendChild(row);

        const select = row.querySelector('.product-select');
        const skuInput = row.querySelector('[name$="[sku]"]');
        const unitInput = row.querySelector('.item-unit-price');

        select.addEventListener('change', () => {
            const opt = select.options[select.selectedIndex];
            skuInput.value = opt.value;
            unitInput.value = opt.dataset.rate;
            console.log(`Product selected: ${opt.text} | Rate: ${opt.dataset.rate}`);
        });

        newItemIndex++;
    });
});
</script>
@endpush
