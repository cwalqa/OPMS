<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Declined - Admin Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h3>Hello Admin,</h3>

    <p>The order <strong>#{{ $order->id }}</strong> placed by <strong>{{ $clientName ?? 'Unknown Client' }}</strong> has been <strong>declined</strong>.</p>

    <hr>

    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Client Name:</strong> {{ $clientName ?? 'N/A' }}</p>
    <p><strong>Client Email:</strong> {{ $order->client_email ?? 'N/A' }}</p>
    <p><strong>Declined At:</strong> {{ now()->toDayDateTimeString() }}</p>

    <p><strong>Reason for Decline:</strong></p>
    <blockquote style="margin-left: 20px; color: #b00020;">
        {!! nl2br(e($reason)) !!}
    </blockquote>

    <p>
        <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" style="color: #c0392b; text-decoration: none; font-weight: bold;">
            ❗ Review Declined Order in Admin Dashboard
        </a>
    </p>

    <hr>

    <p>Best regards,<br><strong>{{ config('app.name') }} System</strong></p>

</body>
</html>

<!-- This email template is used to notify the admin when an order is declined. It includes the order ID, client name, client email, and a link to view the order details in the admin dashboard. The styling is kept simple for clarity and ease of reading. -->