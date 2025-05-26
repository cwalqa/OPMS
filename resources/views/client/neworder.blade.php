@extends('client.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-3 my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-lg border-radius-lg pt-4 pb-3">
                <h5 class="text-white text-capitalize ps-3 mb-0"><i class="fas fa-file-invoice me-2"></i>New Purchase Order</h5>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Progress Indicator -->
            <div class="progress-indicator mb-4">
                <div class="progress" style="height: 8px;">
                    <div id="form-progress" class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="badge bg-success text-white">Step 1: Order Details</span>
                    <span class="badge bg-light text-dark">Step 2: Order Items</span>
                    <span class="badge bg-light text-dark">Step 3: Documentation</span>
                    <span class="badge bg-light text-dark">Step 4: Review</span>
                </div>
            </div>

            <form id="order-form" action="{{ route('client.estimates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="customer_id" value="{{ session()->get('customer.id') }}">
                <input type="hidden" name="customer_ref" value="{{ session()->get('customer.customer_id') }}">
                <!-- Hidden total amount field -->
                <!-- <input type="hidden" name="total_amount" id="total_amount_hidden" value="0"> -->

                <!-- Step 1: Order Details Section -->
                <div id="step-1" class="form-step">
                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="card-body">
                            <h6 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Order Information</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control" value="{{ $poNumber }}" readonly>
                                        <label for="purchase_order_number">System PO Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="po_date" id="po_date" class="form-control" value="{{ date('Y-m-d H:i:s') }}" readonly>
                                        <label for="po_date">Purchase Order Date</label>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-3 text-primary mt-4"><i class="fas fa-user me-2"></i>Client Information</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ session()->get('customer.display_name') }}" readonly>
                                        <label for="customer_name">Client Name</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="email" name="bill_email" id="bill_email" class="form-control" value="{{ session()->get('customer.email') }}" readonly>
                                        <label for="bill_email">Email Address</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="client_po_number" id="client_po_number" class="form-control" placeholder="e.g. PO-2457/2024" required>
                                        <label for="client_po_number">Client PO Number</label>
                                        <div class="invalid-feedback">Please enter your PO number</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary next-step" data-step="1">
                            Next: Order Items <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Order Items Section -->
                <div id="step-2" class="form-step" style="display: none;">
                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="card-body">
                            <h6 class="text-primary mb-3"><i class="fas fa-shopping-cart me-2"></i>Order Items</h6>
                            
                            <!-- Item Quick Add Shortcut -->
                            <div class="mb-3">
                                <label class="form-label">Quick Add Popular Items:</label>
                                <div class="quick-add-buttons">
                                    <!-- Buttons will be filled dynamically via JavaScript -->
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="order-items-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product/Service</th>
                                            <th>Product ID</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-table-body">
                                        <tr class="order-row">
                                            <td>
                                                <select name="product_service[]" class="form-select product_service" required>
                                                    <option value="">Select Product/Service</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->item_id }}" data-rate="{{ $item->unit_price }}">
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">Please select a product or service</div>
                                            </td>
                                            <td>
                                                <input type="text" name="ps_id[]" class="form-control ps_id" readonly />
                                            </td>
                                            <td>
                                                <input type="text" name="description[]" class="form-control description" placeholder="Enter description here..." />
                                                <div class="invalid-feedback">Please enter a description</div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary decrease-qty"><i class="fas fa-minus"></i></button>
                                                    <input type="number" name="quantity[]" min="1" value="1" class="form-control text-center quantity" required />
                                                    <button type="button" class="btn btn-outline-secondary increase-qty"><i class="fas fa-plus"></i></button>
                                                    <div class="invalid-feedback">Please enter a valid quantity</div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <!-- Hidden fields for rate and amount calculation -->
                                                <input type="hidden" name="item_id[]" class="item_id" readonly />
                                                <input type="hidden" name="rate[]" class="rate" value="0" readonly />
                                                <input type="hidden" name="amount[]" class="amount" value="0" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-success add-row">
                                    <i class="fas fa-plus me-1"></i> Add Another Item
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-step="2">
                            <i class="fas fa-arrow-left me-2"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-step="2">
                            Next: Documentation <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Documentation Section -->
                <div id="step-3" class="form-step" style="display: none;">
                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="card-body">
                            <h6 class="text-primary mb-3"><i class="fas fa-file-upload me-2"></i>Upload Documentation</h6>
                            
                            <div class="mb-4">
                                <label for="po_file" class="form-label">Upload PO Document</label>
                                <div class="input-group file-input-group">
                                    <input type="file" name="po_file" id="po_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                    <label class="input-group-text" for="po_file"><i class="fas fa-upload"></i></label>
                                    <div class="invalid-feedback">Please upload your PO document</div>
                                </div>
                                <div class="form-text">Accepted formats: PDF, Word, Excel (Max: 100MB)</div>
                                <div id="file-preview" class="mt-3 d-none">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-0 file-name">document.pdf</h6>
                                                <small class="text-muted file-size">0 KB</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-light ms-auto remove-file">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="customer_memo" class="form-label">Additional Notes</label>
                                <textarea name="customer_memo" id="customer_memo" class="form-control" rows="4" placeholder="Enter any additional notes, special requirements, or delivery instructions..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-step="3">
                            <i class="fas fa-arrow-left me-2"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-step" data-step="3">
                            Next: Review <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Review Section -->
                <div id="step-4" class="form-step" style="display: none;">
                    <div class="card bg-light border-0 mb-4 p-3">
                        <div class="card-body">
                            <h6 class="text-primary mb-3"><i class="fas fa-clipboard-check me-2"></i>Review Your Order</h6>
                            
                            <div class="alert alert-light">
                                <i class="fas fa-info-circle me-2"></i>
                                Please review your order details below. If everything looks correct, click "Submit Order" to finalize your purchase order.
                            </div>
                            
                            <div class="review-section order-info mb-4">
                                <h6 class="text-uppercase text-muted mb-3">Order Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>System PO Number:</strong> <span id="review-po-number"></span></p>
                                        <p><strong>Client PO Number:</strong> <span id="review-client-po-number"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Client Name:</strong> <span id="review-customer-name"></span></p>
                                        <p><strong>Email Address:</strong> <span id="review-email"></span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="review-section order-items mb-4">
                                <h6 class="text-uppercase text-muted mb-3">Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product/Service</th>
                                                <th>ID</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody id="review-items">
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="review-section documentation mb-4">
                                <h6 class="text-uppercase text-muted mb-3">Documentation</h6>
                                <p><strong>Uploaded File:</strong> <span id="review-file-name">None</span></p>
                                <p><strong>Additional Notes:</strong> <span id="review-notes">None</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary prev-step" data-step="4">
                            <i class="fas fa-arrow-left me-2"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success submit-order">
                            <i class="fas fa-check me-2"></i> Submit Order
                        </button>
                    </div>
                </div>

                <!-- Loader Spinner (hidden initially) -->
                <div class="text-center mt-3">
                    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75 d-none" style="z-index: 9999;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- FontAwesome Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Global Form Improvements */
    .form-control:focus, .form-select:focus {
        border-color: #4caf50;
        box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(145deg, #2196f3, #3f51b5);
    }
    
    /* Form Validation Styling */
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    
    .form-control.is-invalid ~ .invalid-feedback,
    .form-select.is-invalid ~ .invalid-feedback {
        display: block;
    }
    
    /* Multi-step form styles */
    .form-step {
        transition: all 0.3s ease;
    }
    
    .progress-indicator {
        margin-bottom: 2rem;
    }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .form-step {
        animation: fadeIn 0.5s ease;
    }
    
    /* File upload styling */
    .file-input-group input[type="file"] {
        position: relative;
        z-index: 2;
    }
    
    /* Review section styling */
    .review-section {
        background-color: #fff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    /* Quick add buttons */
    .quick-add-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .quick-add-btn {
        border-radius: 20px;
        padding: 5px 12px;
        border: 1px solid #ddd;
        background: #f8f9fa;
        transition: all 0.2s;
        cursor: pointer;
        font-size: 0.85rem;
    }
    
    .quick-add-btn:hover {
        background: #e9ecef;
        border-color: #ced4da;
    }
    
    /* Item quantity controls */
    .quantity {
        max-width: 70px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Cache DOM elements
    const orderForm = document.getElementById("order-form");
    const orderTableBody = document.getElementById("order-table-body");
    const submitOrderButton = document.querySelector(".submit-order");
    
    const formProgress = document.getElementById("form-progress");
    const fileInput = document.getElementById("po_file");
    const filePreview = document.getElementById("file-preview");
    const quickAddButtons = document.querySelector(".quick-add-buttons");
    
    // Multi-step form handling
    const steps = document.querySelectorAll(".form-step");
    const nextButtons = document.querySelectorAll(".next-step");
    const prevButtons = document.querySelectorAll(".prev-step");

    const loader = document.getElementById("loader");
    if (loader) {
        console.log("Hiding loader on initialization");
        loader.classList.add("d-none");
    }
    
    // Initialize popular items for quick add
    initQuickAddButtons();
    
    // Pre-set first product if available
    initFirstProduct();
    
    // Set up multi-step form navigation
    nextButtons.forEach(button => {
        button.addEventListener("click", function () {
            const currentStep = parseInt(this.getAttribute("data-step"));

            if (validateStep(currentStep)) {
                const nextStep = currentStep + 1;
                showStep(nextStep);
                updateProgressBar(nextStep * 25);

                if (nextStep === 4) {
                    setTimeout(() => {
                        populateReviewSection();
                    }, 50);
                }
            }
        });
    });

    
    prevButtons.forEach(button => {
        button.addEventListener("click", function() {
            const currentStep = parseInt(this.getAttribute("data-step"));
            showStep(currentStep - 1);
            updateProgressBar((currentStep - 1) * 25);
        });
    });
    
    // Function to show a specific step
    function showStep(stepNumber) {
        steps.forEach((step, index) => {
            if (index + 1 === stepNumber) {
                step.style.display = "block";
                
                // Update step indicators
                document.querySelectorAll(".progress-indicator .badge").forEach((badge, i) => {
                    if (i < stepNumber) {
                        badge.classList.remove("bg-light", "text-dark");
                        badge.classList.add("bg-success", "text-white");
                    } else {
                        badge.classList.remove("bg-success", "text-white");
                        badge.classList.add("bg-light", "text-dark");
                    }
                });
                
                // If we're on the review step, populate the review content
                if (stepNumber === 4) {
                    populateReviewSection();
                }
            } else {
                step.style.display = "none";
            }
        });
    }
    
    // Function to update progress bar
    function updateProgressBar(percentage) {
        formProgress.style.width = percentage + "%";
        formProgress.setAttribute("aria-valuenow", percentage);
    }
    
    // Initialize quick add buttons for popular items
    function initQuickAddButtons() {
        // Get the top 5 most common items (this could come from the server in a real app)
        // For this example, we'll just use the first 5 items
        const productOptions = document.querySelectorAll(".product_service option");
        const topItems = [];
        
        // Skip the first option (which is the placeholder)
        for (let i = 1; i < productOptions.length && i <= 5; i++) {
            if (productOptions[i].value) {
                topItems.push({
                    id: productOptions[i].value,
                    name: productOptions[i].textContent.trim(),
                    rate: productOptions[i].getAttribute("data-rate")
                });
            }
        }
        
        // Create quick add buttons
        topItems.forEach(item => {
            const button = document.createElement("button");
            button.type = "button";
            button.className = "quick-add-btn";
            button.setAttribute("data-id", item.id);
            button.setAttribute("data-rate", item.rate);
            button.innerHTML = `<i class="fas fa-plus-circle me-1"></i>${item.name} (${(item.id)})`;
            
            button.addEventListener("click", function() {
                const itemId = this.getAttribute("data-id");
                quickAddItem(itemId);
            });
            
            quickAddButtons.appendChild(button);
        });
    }
    
    // Quick add an item
    function quickAddItem(itemId) {
        // If this is the first row and it's empty, use it
        const firstRow = document.querySelector(".order-row");
        const firstRowSelect = firstRow.querySelector(".product_service");
        
        if (!firstRowSelect.value) {
            // Set the value for the first row
            firstRowSelect.value = itemId;
            updateItemDetails(firstRow);
            return;
        }
        
        // Otherwise add a new row with this item pre-selected
        addRow(itemId);
    }
    
    // Function to add a new row
    function addRow(preSelectedItemId = null) {
        const originalRow = document.querySelector(".order-row"); // Get the original row
        const newRow = originalRow.cloneNode(true); // Clone the original row

        // Reset the inputs of the cloned row
        newRow.querySelectorAll('input').forEach(input => {
            if (input.name.includes('quantity')) {
                input.value = '1'; // Set quantity to default value of 1
            } else {
                input.value = '';
            }
            input.classList.remove('is-invalid');
        });

        // Reset or pre-select the dropdown
        const selectEl = newRow.querySelector('.product_service');
        if (preSelectedItemId) {
            selectEl.value = preSelectedItemId;
        } else {
            selectEl.selectedIndex = 0;
        }
        selectEl.classList.remove('is-invalid');

        // Attach the new row to the table
        orderTableBody.appendChild(newRow);
        
        // If we pre-selected an item, update its details
        if (preSelectedItemId) {
            updateItemDetails(newRow);
        }
        
        // Scroll to the bottom of the table
        newRow.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    // Function to initialize first product
    function initFirstProduct() {
        const firstRow = document.querySelector(".order-row");
        const productSelect = firstRow.querySelector(".product_service");
        
        // Select first product if available (skip the placeholder option)
        if (productSelect.options.length > 1) {
            productSelect.selectedIndex = 0;
            updateItemDetails(firstRow);
        }
    }

    // Handle row removal and addition using event delegation
    document.addEventListener("click", function (e) {
        const addButton = e.target.closest(".add-row");
        const removeButton = e.target.closest(".remove-row");
        const decreaseQty = e.target.closest(".decrease-qty");
        const increaseQty = e.target.closest(".increase-qty");
        const removeFile = e.target.closest(".remove-file");
        
        if (addButton) {
            addRow();
            return;
        }
        
        if (removeButton) {
            const row = removeButton.closest("tr");
            if (document.querySelectorAll(".order-row").length > 1) {
                // Add a fade-out animation
                row.style.transition = "opacity 0.3s";
                row.style.opacity = "0";
                
                // Remove after animation completes
                setTimeout(() => {
                    row.remove();
                    calculateOrderTotal(); // Recalculate total after removal
                }, 300);
            }
            return;
        }
        
        if (decreaseQty) {
            const qtyInput = decreaseQty.closest('.input-group').querySelector('.quantity');
            const currentQty = parseInt(qtyInput.value);
            if (currentQty > 1) {
                qtyInput.value = currentQty - 1;
                updateRowTotal(decreaseQty.closest('tr')); // Update row total
                calculateOrderTotal(); // Recalculate order total
            }
            return;
        }
        
        if (increaseQty) {
            const qtyInput = increaseQty.closest('.input-group').querySelector('.quantity');
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateRowTotal(increaseQty.closest('tr')); // Update row total
            calculateOrderTotal(); // Recalculate order total
            return;
        }
        
        if (removeFile) {
            fileInput.value = '';
            filePreview.classList.add('d-none');
            return;
        }
    });

    // Handle quantity changes
    orderTableBody.addEventListener("input", function (e) {
        if (e.target.classList.contains("quantity")) {
            validateField(e.target);
            updateRowTotal(e.target.closest('tr')); // Update row total when quantity changes
            calculateOrderTotal(); // Recalculate order total
        }
    });

    // Update item details on product_service change
    orderTableBody.addEventListener("change", function (e) {
        if (e.target.classList.contains("product_service")) {
            const row = e.target.closest("tr");
            updateItemDetails(row);
            validateField(e.target);
            updateRowTotal(row); // Update row total when product changes
            calculateOrderTotal(); // Recalculate order total
        }
    });

    // Field validation
    function validateField(field) {
        if (!field.checkValidity()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
            return true;
        }
    }

    // Add blur validation for required fields
    orderForm.addEventListener("blur", function(e) {
        if (e.target.hasAttribute('required')) {
            validateField(e.target);
        }
    }, true);

    // Function to update item details based on selected product_service
    function updateItemDetails(row) {
        const productServiceSelect = row.querySelector(".product_service");
        const selectedOption = productServiceSelect.options[productServiceSelect.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const itemId = selectedOption.value;
            const rate = selectedOption.getAttribute("data-rate");

            row.querySelector(".item_id").value = itemId;
            row.querySelector(".rate").value = rate;
            row.querySelector(".ps_id").value = itemId;
            
            // Hide the rate display in the row since we're hiding rates
            const rateDisplay = row.querySelector(".rate-display");
            if (rateDisplay) {
                rateDisplay.textContent = "***";
            }
            
            // Update the row total (but it will be hidden in the UI)
            updateRowTotal(row);
        } else {
            row.querySelector(".item_id").value = '';
            row.querySelector(".rate").value = '';
            row.querySelector(".ps_id").value = '';
            
            // Clear the rate display in the row
            const rateDisplay = row.querySelector(".rate-display");
            if (rateDisplay) {
                rateDisplay.textContent = "***";
            }
            
            // Update the row total
            updateRowTotal(row);
        }
    }
    
    // Function to update a row's total amount
    function updateRowTotal(row) {
        const quantity = parseFloat(row.querySelector(".quantity").value) || 0;
        const rate = parseFloat(row.querySelector(".rate").value) || 0;
        const amount = quantity * rate;
        
        const amountInput = row.querySelector(".amount");
        const amountDisplay = row.querySelector(".amount-display");
        
        if (amountInput) {
            amountInput.value = amount.toFixed(2);
        }
        
        if (amountDisplay) {
            amountDisplay.textContent = "***"; // Hide the actual amount
        }
    }
    
    // Function to calculate the total order amount (but hide it in the UI)
    function calculateOrderTotal() {
        let total = 0;
        const rows = document.querySelectorAll(".order-row");
        
        rows.forEach(row => {
            const amountInput = row.querySelector(".amount");
            if (amountInput && amountInput.value) {
                total += parseFloat(amountInput.value) || 0;
            }
        });
        
        // Update the total display - but hide the actual amount
        const totalDisplay = document.getElementById("order-total-display");
        const totalInput = document.getElementById("order-total");
        
        if (totalDisplay) {
            totalDisplay.textContent = "***"; // Hide the actual total
        }
        
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
        
        return total;
    }
    
    // Helper function to format currency (not displayed to client)
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Handle file input changes
    fileInput.addEventListener("change", function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            // Update the preview
            const fileName = document.querySelector('.file-name');
            const fileSize = document.querySelector('.file-size');
            
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            // Show the icon based on file type
            const fileIcon = document.querySelector('#file-preview i');
            if (file.name.endsWith('.pdf')) {
                fileIcon.className = 'fas fa-file-pdf text-danger fa-2x me-3';
            } else if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                fileIcon.className = 'fas fa-file-word text-primary fa-2x me-3';
            } else if (file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) {
                fileIcon.className = 'fas fa-file-excel text-success fa-2x me-3';
            } else {
                fileIcon.className = 'fas fa-file-alt text-secondary fa-2x me-3';
            }
            
            filePreview.classList.remove('d-none');
        } else {
            filePreview.classList.add('d-none');
        }
    });
    
    // Helper function to format file sizes
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Validate a specific step
    function validateStep(stepNumber) {
        let isValid = true;
        
        // Get all required fields in this step
        const currentStepElement = document.getElementById(`step-${stepNumber}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        // Additional validation logic for specific steps
        if (stepNumber === 2) {
            // Make sure at least one item is selected
            const productSelects = currentStepElement.querySelectorAll('.product_service');
            let hasSelectedProduct = false;
            
            productSelects.forEach(select => {
                if (select.value) {
                    hasSelectedProduct = true;
                }
            });
            
            if (!hasSelectedProduct) {
                isValid = false;
                // Show a message about selecting at least one product
                Swal.fire({
                    title: 'Missing Items',
                    text: 'Please select at least one product or service for your order.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        }
        
        if (!isValid) {
            // Show general validation error
            if (stepNumber !== 2) { // Skip this for step 2 as we already showed a more specific message
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Please fill in all required fields correctly before proceeding.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
        
        return isValid;
    }
    
    // Populate the review section with form data
    function populateReviewSection() {
        // Basic info
        document.getElementById('review-po-number').textContent = document.getElementById('purchase_order_number').value;
        document.getElementById('review-client-po-number').textContent = document.getElementById('client_po_number').value;
        document.getElementById('review-customer-name').textContent = document.getElementById('customer_name').value;
        document.getElementById('review-email').textContent = document.getElementById('bill_email').value;
        
        // Order items
        const reviewItemsTable = document.getElementById('review-items');
        reviewItemsTable.innerHTML = '';
        
        const itemRows = document.querySelectorAll('.order-row');
        
        itemRows.forEach(row => {
            const productSelect = row.querySelector('.product_service');
            const productId = row.querySelector('.ps_id').value;
            const description = row.querySelector('.description').value;
            const quantity = row.querySelector('.quantity').value;
            
            if (productSelect.value) {
                const productName = productSelect.options[productSelect.selectedIndex].textContent;
                
                const reviewRow = document.createElement('tr');
                reviewRow.innerHTML = `
                    <td>${productName}</td>
                    <td>${productId}</td>
                    <td>${description || 'N/A'}</td>
                    <td>${quantity}</td>
                `;
                
                reviewItemsTable.appendChild(reviewRow);
            }
        });
        
        // File and notes
        let fileName = 'None';
        if (fileInput.files && fileInput.files.length > 0) {
            fileName = fileInput.files[0].name;
        }
        document.getElementById('review-file-name').textContent = fileName;
        
        const notes = document.getElementById('customer_memo').value;
        document.getElementById('review-notes').textContent = notes || 'None';
    }

    // Handle form submission with validation
    orderForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        // Final validation of all required fields
        let isValid = true;
        const requiredFields = orderForm.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Don't proceed if validation fails
        if (!isValid) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields correctly.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Confirm submission
        Swal.fire({
            title: 'Submit Order?',
            text: 'Please confirm that you want to submit this purchase order.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit Order',
            cancelButtonText: 'No, Review Again',
            confirmButtonColor: '#4caf50'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show the loader
                if (loader) {
                    console.log("Showing loader for form submission");
                    loader.classList.remove("d-none");
                }
                submitOrderButton.disabled = true;

                // Submit the form data using AJAX (fetch API)
                fetch(orderForm.action, {
                    method: "POST",
                    body: new FormData(orderForm),
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (loader) {
                        console.log("Hiding loader after successful submission");
                        loader.classList.add("d-none");
                    }

                    if (data.success) {
                        Swal.fire({
                            title: 'Order Submitted Successfully!',
                            text: data.success,
                            icon: 'success',
                            confirmButtonText: 'View Orders',
                            confirmButtonColor: '#4caf50'
                        }).then(() => {
                            window.location.href = "{{ route('client.purchaseorder') }}"; // Redirect on success
                        });
                    } else if (data.error) {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    if (loader) {
                        console.log("Hiding loader on error");
                        loader.classList.add("d-none");
                    }
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred during form submission. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    });

    window.addEventListener("error", function (event) {
        console.error("Uncaught error:", event.error);
        const loader = document.getElementById("loader");
        if (loader) {
            console.log("Hiding loader on uncaught error");
            loader.classList.add("d-none");
        }
    });
});
</script>