<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Acknowledgment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; }
        h1 { font-size: 24px; color: #333; }
        p { font-size: 16px; color: #555; line-height: 1.6; }
        .order-details { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .order-details h3 { margin: 0; font-size: 18px; color: #333; }
        ul { padding-left: 20px; }
        li { font-size: 14px; color: #555; }
        .footer { font-size: 12px; color: #999; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank you for your order, {{ $estimate->customer_name }}!</h1>
        <p>We have received your order and are currently processing it. Here are the details:</p>

        <div class="order-details">
            <h3>Order Information</h3>
            <p><strong>Order Date:</strong> {{ $estimate->created_at->format('F j, Y') }}</p>
            <p><strong>Order Number:</strong> {{ $estimate->purchase_order_number }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($estimate->total_amount, 2) }}</p>
            <p><strong>Customer Notes:</strong> {{ $estimate->customer_memo ?? 'None provided' }}</p>
        </div>

        <h3>Order Items:</h3>
        <ul>
            @foreach ($estimateItems as $item)
                <li>
                    <strong>{{ $item->product_name ?? 'Unnamed Item' }}</strong> <br>
                    Product ID: {{ $item->sku }}<br>
                    Quantity: {{ $item->quantity }}<br>
                    Unit Price: ${{ number_format($item->unit_price, 2) }}<br>
                    Total: ${{ number_format($item->amount, 2) }}
                </li>
            @endforeach
        </ul>


        <p>Thank you for your business! You will receive a notification on the status of your order.</p>
        <p>If you have any questions, please to contact us at <a href="mailto:info@datapluzz.com">info@datapluzz.com</p>
        <p>Best regards,</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ColorWrap Inc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
