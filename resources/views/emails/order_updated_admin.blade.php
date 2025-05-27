<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->purchase_order_number }} Has Been Modified</title>
</head>
<body>
    <h2>Order #{{ $order->purchase_order_number }} Has Been Modified</h2>
    <p>Dear Admin,</p>
    <p>An order has been updated successfully. Below are the details of the updated items and new items added:</p>

    <h3>Updated Items:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product ID</th>
                <th>Description</th>
                <th>Old Quantity</th>
                <th>New Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($updatedItems as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['product_id'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['old_quantity'] }}</td>
                    <td>{{ $item['new_quantity'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Added Items:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product ID</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($addedItems as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['product_id'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Customer Memo:</strong> {{ $order->customer_memo }}</p>

    <p>If you have any questions or need to take further action, please review the updated order.</p>
    <p>Best regards, <br> ColorWrap Inc</p>
</body>
</html>
