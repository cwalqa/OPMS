@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Estimate</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('estimates.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="customer_name">Client Name</label>
            <select name="customer_name" id="customer_name" class="form-control" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->display_name }}" data-email="{{ $customer->email }}">{{ $customer->display_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="bill_email">Client Email Address</label>
            <input type="email" name="bill_email" id="bill_email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="product_service">Product/Service</label>
            <select name="product_service" id="product_service" class="form-control" required>
                <option value="">Select Product/Service</option>
                @foreach($items as $item)
                    <option value="{{ $item->fully_qualified_name }}" data-item-id="{{ $item->item_id }}">{{ $item->fully_qualified_name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="item_id" id="item_id" required>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="customer_memo">Notes / Memo</label>
            <textarea name="customer_memo" id="customer_memo" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Order</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update email field when customer is selected
    document.getElementById('customer_name').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var emailField = document.getElementById('bill_email');
        if (selectedOption) {
            var email = selectedOption.getAttribute('data-email');
            emailField.value = email;
        } else {
            emailField.value = '';
        }
    });

    // Function to update item_id field when product/service is selected
    document.getElementById('product_service').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var itemIdField = document.getElementById('item_id');
        if (selectedOption) {
            var itemId = selectedOption.getAttribute('data-item-id');
            itemIdField.value = itemId;
        } else {
            itemIdField.value = '';
        }
    });

    @if (session('success'))
        Swal.fire({
            title: 'Success!',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
@endsection
