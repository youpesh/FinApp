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
            background-color: #4f46e5;
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

        .credentials {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin: 15px 0;
        }

        .credentials strong {
            color: #374151;
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
        <h1>Welcome to Smart Finance</h1>
    </div>
    <div class="content">
        <p>Dear {{ $user->full_name }},</p>

        <p>Your access request to the Smart Finance system has been <strong>approved</strong>. You can now log in with
            the credentials below:</p>

        <div class="credentials">
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
        </div>

        <p><strong>Important:</strong> You will be required to change your password upon first login for security
            purposes.</p>

        <a href="{{ url('/login') }}" class="btn">Login to Smart Finance</a>

        <p style="margin-top: 20px;">If you did not request access, please disregard this email.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Smart Finance. All rights reserved.</p>
    </div>
</body>

</html>