<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order_number }} Has Been Modified</title>
</head>
<body>
    <h2>Order #{{ $order_number }} Has Been Modified</h2>
    <p>Dear Admin,</p>
    <p>An order has been updated successfully. Below are the details of the updated items and new items added:</p>

    <h3>Updated Items:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Old Quantity</th>
                <th>New Quantity</th>
                <th>Unit Price</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($updated_items as $item)
                <tr>
                    <td>{{ $item['product_id'] }}</td>
                    <td>{{ $item['old_quantity'] }}</td>
                    <td>{{ $item['new_quantity'] }}</td>
                    <td>{{ $item['unit_price'] }}</td>
                    <td>{{ $item['total_cost'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Added Items:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($added_items as $item)
                <tr>
                    <td>{{ $item['product_id'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['unit_price'] }}</td>
                    <td>{{ $item['total_cost'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Amount: {{ $total_amount }}</h3>
    <p><strong>Customer Memo:</strong> {{ $customer_memo }}</p>

    <p>If you have any questions or need to take further action, please review the updated order.</p>
    <p>Best regards, <br> ColorWrap Inc</p>
</body>
</html>
