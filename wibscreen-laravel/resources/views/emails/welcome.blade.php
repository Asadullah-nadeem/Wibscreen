<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #999; }
        .button { display: inline-block; padding: 12px 24px; background-color: #6366f1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" width="50">
            <h2>Welcome to Wibscreen!</h2>
        </div>
        <p>Hi {{ $user->name }},</p>
        <p>Thank you for joining Wibscreen! We're excited to help you build your ultimate browser workspace.</p>
        <p>With Wibscreen, you can organize your tabs, collections, and work more efficiently than ever before.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('dashboard') }}" class="button">Go to Dashboard</a>
        </div>
        <p>If you have any questions, just reply to this email.</p>
        <p>Best regards,<br>The Wibscreen Team</p>
        <div class="footer">
            &copy; {{ date('Y') }} Wibscreen by CodeAxe Technologies. All rights reserved.
        </div>
    </div>
</body>
</html>
