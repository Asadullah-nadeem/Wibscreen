<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #999; }
        .details { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" width="50">
            <h2 style="color: #22c55e;">Payment Successful!</h2>
        </div>
        <p>Hi {{ $user->name }},</p>
        <p>Your payment was successful and your <strong>{{ ucfirst($plan) }} Plan</strong> is now active!</p>
        
        <div class="details">
            <p><strong>Plan:</strong> {{ ucfirst($plan) }}</p>
            <p><strong>Duration:</strong> {{ $duration }}</p>
            @if($paymentId)
                <p><strong>Payment ID:</strong> {{ $paymentId }}</p>
            @endif
            <p><strong>Status:</strong> Active</p>
        </div>

        <p>Thank you for choosing Wibscreen Pro. You now have access to unlimited workspaces, tabs, and more.</p>
        
        <p>Best regards,<br>The Wibscreen Team</p>
        <div class="footer">
            &copy; {{ date('Y') }} Wibscreen by CodeAxe Technologies. All rights reserved.
        </div>
    </div>
</body>
</html>
