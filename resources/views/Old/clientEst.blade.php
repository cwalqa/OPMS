@extends('layouts.estimate')

@section('content')
    <!-- Start Order Entry -->
    <div class="container">
        <div class="title">Please complete the Purchase Order Form</div>

        <!-- Logout Button -->
        <div class="logout-container" style="text-align: right; margin-bottom: 20px;">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

       

        <form id="order-form" action="{{ route('estimates.store') }}" method="POST">
            @csrf

            <!-- Client Details -->
             <!-- Hidden field for customer_id -->
            <input type="hidden" name="customer_id" value="{{ session('customer.customer_id') }}">

            <div class="row mb-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="customer_name">Client Name</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ session('customer.display_name') }}" readonly>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="form-group">
                        <label for="customer_name">Client ID</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ session('customer.customer_id') }}" readonly>
                    </div>
                </div> -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bill_email">Email Address</label>
                        <input type="text" name="bill_email" id="bill_email" class="form-control" value="{{ session('customer.email') }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="po_date">Purchase Order Date</label>
                        <input type="text" name="po_date" id="po_date" class="form-control" value="{{ date('Y-m-d H:i:s') }}" readonly>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Quantity</th>
                        <th>Item No</th>
                        <th>Description</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="order-table-body">
                    <tr>
                        <td><input type="number" name="quantity[]" min="1" value="1" class="quantity" required></td>
                        <td><input type="text" name="item_no[]" class="item_no" readonly></td>
                        <td>
                            <select name="description[]" class="description" required>
                                <option value="">Select Product/Service</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->fully_qualified_name }}" data-item-id="{{ $item->item_id }}" data-rate="{{ $item->unit_price }}">
                                        {{ $item->fully_qualified_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="rate[]" class="rate" readonly></td>
                        <td><input type="text" name="amount[]" class="amount" readonly></td>
                        <td><span class="remove-row">Remove</span></td>
                        <!-- Hidden field for item_id -->
                        <input type="hidden" name="item_id[]" class="item_id">
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                        <td><input type="text" id="total-amount" readonly></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Notes/Memo Field -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="order-notes">Notes / Memo:</label>
                        <textarea name="customer_memo" id="customer_memo" class="form-control" rows="4" placeholder="Enter any additional notes or memo for your order..."></textarea>
                    </div>
                </div>
            </div>

            <button type="button" class="add-row">Add Row</button>
            <button type="submit" class="submit-order">Submit Order</button>
        </form>
    </div>
    <!-- End Order Entry -->

    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const orderTableBody = document.getElementById("order-table-body");
        const addRowButton = document.querySelector(".add-row");
        const submitOrderButton = document.querySelector(".submit-order");
        const totalAmountField = document.getElementById("total-amount");
        const orderForm = document.getElementById("order-form");

        // Function to add a new row to the table
        function addRow() {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td><input type="number" name="quantity[]" min="1" value="1" class="quantity" required></td>
                <td><input type="text" name="item_no[]" class="item_no" readonly></td>
                <td>
                    <select name="description[]" class="description" required>
                        <option value="">Select Product/Service</option>
                        @foreach($items as $item)
                            <option value="{{ $item->fully_qualified_name }}" data-item-id="{{ $item->item_id }}" data-rate="{{ $item->unit_price }}">
                                {{ $item->fully_qualified_name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="rate[]" class="rate" readonly></td>
                <td><input type="text" name="amount[]" class="amount" readonly></td>
                <td><span class="remove-row">Remove</span></td>
                <!-- Hidden field for item_id -->
                <input type="hidden" name="item_id[]" class="item_id">
            `;
            orderTableBody.appendChild(newRow);
        }

        // Add event listener to the Add Row button
        addRowButton.addEventListener("click", addRow);

        // Add event listener to the table to handle input changes and row removal
        orderTableBody.addEventListener("input", function (e) {
            if (e.target.classList.contains("quantity") || e.target.classList.contains("rate")) {
                const row = e.target.closest("tr");
                updateAmount(row);
                updateTotal();
            }
        });

        orderTableBody.addEventListener("change", function (e) {
            if (e.target.classList.contains("description")) {
                const row = e.target.closest("tr");
                updateItemDetails(row);
                updateAmount(row);
                updateTotal();
            }
        });

        orderTableBody.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-row")) {
                const row = e.target.closest("tr");
                row.remove();
                updateTotal();
            }
        });

        // Function to update item details based on selected description
        function updateItemDetails(row) {
            const descriptionSelect = row.querySelector(".description");
            const selectedOption = descriptionSelect.options[descriptionSelect.selectedIndex];
            const itemNoField = row.querySelector(".item_no");
            const rateField = row.querySelector(".rate");
            const itemIdField = row.querySelector(".item_id");

            if (selectedOption) {
                itemNoField.value = selectedOption.value; // Display fully_qualified_name
                rateField.value = selectedOption.getAttribute("data-rate");
                itemIdField.value = selectedOption.getAttribute("data-item-id"); // Store item_id in hidden field
            } else {
                itemNoField.value = '';
                rateField.value = '';
                itemIdField.value = '';
            }
        }

        // Function to update the amount field based on quantity and rate
        function updateAmount(row) {
            const quantityField = row.querySelector(".quantity");
            const rateField = row.querySelector(".rate");
            const amountField = row.querySelector(".amount");

            const quantity = parseFloat(quantityField.value) || 0;
            const rate = parseFloat(rateField.value) || 0;
            const amount = quantity * rate;

            amountField.value = amount.toFixed(2); // Format to 2 decimal places
        }

        // Function to calculate and update the total amount
        function updateTotal() {
            let total = 0;

            document.querySelectorAll(".amount").forEach(amountField => {
                total += parseFloat(amountField.value) || 0;
            });

            totalAmountField.value = total.toFixed(2); // Format to 2 decimal places
        }

        // Add event listener to the form submit event
        orderForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent the default form submission

            // Use SweetAlert to display a success message
            Swal.fire({
                title: 'Order Submitted!',
                text: 'Your order has been successfully submitted.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with form submission after showing SweetAlert
                    orderForm.submit();
                }
            });
        });
    });
    </script>

@endsection
