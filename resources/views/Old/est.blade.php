@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Estimate</h1>


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
                @foreach($items as $item)
                    <option value="{{ $item->fully_qualified_name }}">{{ $item->fully_qualified_name }}</option>
                @endforeach
            </select>
        </div>

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
});
</script>

@endsection
