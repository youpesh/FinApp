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

        .details {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin: 15px 0;
        }

        .details p {
            margin: 5px 0;
        }

        .details strong {
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
        <h1>New Access Request</h1>
    </div>
    <div class="content">
        <p>A new user has submitted an access request to Smart Finance. Please review and approve or deny the request.
        </p>

        <div class="details">
            <p><strong>Name:</strong> {{ $accessRequest->first_name }} {{ $accessRequest->last_name }}</p>
            <p><strong>Email:</strong> {{ $accessRequest->email }}</p>
            @if($accessRequest->address)
                <p><strong>Address:</strong> {{ $accessRequest->address }}</p>
            @endif
            @if($accessRequest->dob)
                <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($accessRequest->dob)->format('F j, Y') }}</p>
            @endif
            <p><strong>Submitted:</strong> {{ $accessRequest->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>

        <a href="{{ url('/admin/requests') }}" class="btn">Review Access Requests</a>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Smart Finance. All rights reserved.</p>
    </div>
</body>

</html>