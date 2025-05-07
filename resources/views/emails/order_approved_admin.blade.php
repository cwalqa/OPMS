<!DOCTYPE html>
<html>
<head>
    <title>Order Approved - Admin Notification</title>
</head>
<body>
    <h3>Hello Admin,</h3>

    <p>The order <strong>#{{ $order->id }}</strong> placed by <strong>{{ $clientName }}</strong> has been <strong>approved</strong>.</p>

    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Client Name:</strong> {{ $clientName }}</p>
    <p><strong>Client Email:</strong> {{ $order->client_email ?? 'N/A' }}</p>
    <p><strong>Approved At:</strong> {{ now()->toDayDateTimeString() }}</p>


    <p>Best regards, <br> {{ config('app.name') }} System</p>
</body>
</html>
<!-- This email template is used to notify the admin when an order is approved. It includes the order ID, client name, client email, and a link to view the order details in the admin dashboard. The styling is kept simple for clarity and ease of reading. -->