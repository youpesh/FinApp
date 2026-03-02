<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f59e0b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .warning-box {
            background-color: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>⚠️ Password Expiry Warning</h1>
    </div>
    <div class="content">
        <p>Dear {{ $user->full_name }},</p>

        <div class="warning-box">
            <p><strong>Your password will expire in {{ $daysRemaining }}
                    {{ $daysRemaining === 1 ? 'day' : 'days' }}.</strong></p>
            <p>Password expiry date: {{ $user->password_expires_at->format('F j, Y \a\t g:i A') }}</p>
        </div>

        <p>Please log in and update your password before it expires to avoid being locked out.</p>

        <a href="{{ url('/profile') }}" class="btn">Update Password Now</a>

        <p style="margin-top: 20px;">If you need assistance, please contact your administrator.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Smart Finance. All rights reserved.</p>
    </div>
</body>

</html>