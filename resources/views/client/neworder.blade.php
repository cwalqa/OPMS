@extends('client.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">NEW PURCHASE ORDER</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-3">
                        <form id="order-form" action="{{ route('client.estimates.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ session()->get('customer.id') }}">
                            <input type="hidden" name="customer_ref" value="{{ session()->get('customer.customer_id') }}">

                            <!-- Purchase Order Number -->
                            <div class="row d-flex justify-content-center align-items-center mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="purchase_order_number"><b>Purchase Order Number</b></label>
                                        <input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control" value="{{ $poNumber }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="customer_name"><b>Client Name</b></label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ session()->get('customer.display_name') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bill_email"><b>Email Address</b></label>
                                        <input type="email" name="bill_email" id="bill_email" class="form-control" value="{{ session()->get('customer.email') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="po_date"><b>Purchase Order Date</b></label>
                                        <input type="text" name="po_date" id="po_date" class="form-control" value="{{ date('Y-m-d H:i:s') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items Table -->
                            <h6 class="text-center mt-5 mb-3"><b>ORDER ITEMS</b></h6>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product/Service</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product ID</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-table-body">
                                        <tr class="order-row">
                                            <td>
                                                <select name="product_service[]" class="form-control product_service" required>
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
                                                <input type="number" name="quantity[]" min="1" value="1" class="form-control quantity" required />
                                                <div class="invalid-feedback">Please enter a valid quantity</div>
                                            </td>
                                            <td>
                                                <input type="text" name="amount[]" class="form-control amount" readonly />
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary add-row me-1">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger remove-row">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </td>
                                            <input type="hidden" name="item_id[]" class="item_id" readonly />
                                            <input type="hidden" name="rate[]" class="rate" readonly />
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><input type="text" name="total_amount" id="total_amount" class="form-control" readonly /></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Additional notes -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="customer_memo"><b>Additional Notes</b></label>
                                    <textarea name="customer_memo" id="customer_memo" class="form-control" rows="4" placeholder="Enter any additional notes..."></textarea>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" class="btn btn-success submit-order">Submit Order</button>

                            <!-- Loader Spinner (hidden initially) -->
                            <div class="text-center mt-3">
                                <div id="loader" style="display:none;">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- FontAwesome Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    
    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const orderTableBody = document.getElementById("order-table-body");
        const totalAmountField = document.getElementById("total_amount");
        const orderForm = document.getElementById("order-form");
        const submitOrderButton = document.querySelector(".submit-order");
        const loader = document.getElementById("loader");

        // Pre-set first product if available
        initFirstProduct();
        
        // Function to add a new row
        function addRow() {
            const originalRow = document.querySelector(".order-row"); // Get the original row
            const newRow = originalRow.cloneNode(true); // Clone the original row

            // Reset the inputs of the cloned row
            newRow.querySelectorAll('input').forEach(input => {
                if (input.name === 'quantity[]') {
                    input.value = '1'; // Set quantity to default value of 1
                } else {
                    input.value = '';
                }
                input.classList.remove('is-invalid');
            });

            // Reset the select element
            const selectEl = newRow.querySelector('.product_service');
            selectEl.selectedIndex = 0;
            selectEl.classList.remove('is-invalid');

            // Attach the new row to the table
            orderTableBody.appendChild(newRow);
        }

        // Function to initialize first product
        function initFirstProduct() {
            const firstRow = document.querySelector(".order-row");
            const productSelect = firstRow.querySelector(".product_service");
            
            // Select first product if available (skip the placeholder option)
            if (productSelect.options.length > 1) {
                productSelect.selectedIndex = 1;
                updateItemDetails(firstRow);
                updateAmount(firstRow);
                updateTotal();
            }
        }

        // Handle row removal and addition
        orderTableBody.addEventListener("click", function (e) {
            const target = e.target.closest("button");
            if (!target) return;
            
            if (target.classList.contains("add-row")) {
                addRow();
            } else if (target.classList.contains("remove-row")) {
                const row = target.closest("tr");
                if (document.querySelectorAll(".order-row").length > 1) {
                    row.remove();
                    updateTotal();
                }
            }
        });

        // Update amount and total on input changes
        orderTableBody.addEventListener("input", function (e) {
            if (e.target.classList.contains("quantity")) {
                const row = e.target.closest("tr");
                updateAmount(row);
                updateTotal();
                validateField(e.target);
            }
        });

        // Update item details and amount on product_service change
        orderTableBody.addEventListener("change", function (e) {
            if (e.target.classList.contains("product_service")) {
                const row = e.target.closest("tr");
                updateItemDetails(row);
                updateAmount(row);
                updateTotal();
                validateField(e.target);
            }
        });

        // Field validation
        function validateField(field) {
            if (!field.checkValidity()) {
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
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
            const product_serviceSelect = row.querySelector(".product_service");
            const selectedOption = product_serviceSelect.options[product_serviceSelect.selectedIndex];

            if (selectedOption && selectedOption.value) {
                const itemId = selectedOption.value;
                const rate = selectedOption.getAttribute("data-rate");

                row.querySelector(".item_id").value = itemId;   // ✅ Fix: set this explicitly
                row.querySelector(".rate").value = rate;
                row.querySelector(".ps_id").value = itemId;
            } else {
                row.querySelector(".item_id").value = '';       // clear on invalid
                row.querySelector(".rate").value = '';
                row.querySelector(".ps_id").value = '';
            }
        }

        // Function to update the amount for a specific row
        function updateAmount(row) {
            const quantity = parseFloat(row.querySelector(".quantity").value) || 0;
            const rate = parseFloat(row.querySelector(".rate").value) || 0;
            const amountField = row.querySelector(".amount");
            const amount = quantity * rate;
            amountField.value = amount.toFixed(2);
        }

        // Function to update the total amount
        function updateTotal() {
            let total = 0;
            const amountFields = orderTableBody.querySelectorAll(".amount");
            amountFields.forEach(field => {
                total += parseFloat(field.value) || 0;
            });
            totalAmountField.value = total.toFixed(2);
        }

        // Handle form submission with validation
        orderForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            // Validate all required fields
            let isValid = true;
            const requiredFields = orderForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
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

            // Show the loader
            loader.style.display = "block";
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
                loader.style.display = "none"; // Hide the loader
                submitOrderButton.disabled = false;

                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.success,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "{{ route('client.purchaseorder') }}"; // Redirect on success
                    });
                } else if (data.error) {
                    Swal.fire({
                        title: 'Error!',
                        text: data.error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                loader.style.display = "none"; // Hide the loader
                submitOrderButton.disabled = false;

                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred during form submission.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    });
</script>