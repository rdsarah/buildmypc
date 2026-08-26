<!DOCTYPE html>
<html>
<head>
    <title>Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #0d1a2f; border-bottom: 2px solid #00f0ff; padding-bottom: 10px;">Update on Your BuildMyPC Order</h2>
        
        <p>Hello {{ $order->user->name ?? 'Customer' }},</p>
        
        <p>The status of your order <strong>#{{ $order->id }}</strong> has been updated to:</p>
        
        <h3 style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #00f0ff; display: inline-block;">
            {{ ucfirst($order->status) }}
        </h3>
        
        <p style="margin-top: 20px;">
            @if($order->status == 'pending')
                Your order has been received and is currently pending review. We will notify you once it begins processing.
            @elseif($order->status == 'processing')
                We are currently processing your order and getting it ready for shipment.
            @elseif($order->status == 'shipped')
                Your order has been shipped and is on its way to you!
            @elseif($order->status == 'delivered')
                Your order has been marked as delivered. We hope you enjoy your new PC components!
            @elseif($order->status == 'cancelled')
                Your order has been cancelled. If you have any questions, please contact our support team.
            @endif
        </p>

        <hr style="border: none; border-top: 1px solid #eaeaea; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #777;">
            Thank you for shopping with BuildMyPC!<br>
            If you need further assistance, please log in to your account to view your order history.
        </p>
    </div>
</body>
</html>
