<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Order Has Been Declined</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h3>Dear {{ $clientName ?? 'Client' }},</h3>

    <p>We regret to inform you that your order <strong>#{{ $order->id }}</strong> has been <strong>declined</strong>.</p>

    <p><strong>Reason for Decline:</strong></p>
    <blockquote style="margin-left: 20px; color: #b00020;">
        {!! nl2br(e($reason)) !!}
    </blockquote>

    <p>If you believe this was a mistake or require further information, please don't hesitate to contact our support team.</p>

    <p>Thank you for your interest in {{ config('app.name') }}.</p>

    <p>Best regards,<br>
    <strong>{{ config('app.name') }} Team</strong></p>

</body>
</html>
