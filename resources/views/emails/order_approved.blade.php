<!DOCTYPE html>
<html>
<head>
    <title>Order Approved</title>
</head>
<body>
    <h3>Dear {{ $clientName }},</h3>
    <p>Your order <strong>#{{ $order->id }}</strong> has been <strong>approved</strong>.</p>
    <p>Thank you for your order!</p>
    <p>Best regards, <br> {{ config('app.name') }} Team</p>
</body>
</html>
