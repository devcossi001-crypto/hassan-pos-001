<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .maintenance-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
        }
        .maintenance-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .reason-box {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-left: 4px solid #dc3545;
            margin: 2rem 0;
            text-align: left;
        }
        .reason-box h5 {
            color: #dc3545;
            margin-bottom: 0.5rem;
        }
        .reason-box p {
            margin: 0.25rem 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="bi bi-exclamation-circle"></i>
            ⚠️
        </div>
        <h1>System Maintenance</h1>
        <p class="lead text-muted">
            The system is currently unavailable.
        </p>

        @if (isset($reason))
            <div class="reason-box">
                <h5>{{ $reason }}</h5>
            </div>
        @elseif ($systemStatus->status_reason)
            <div class="reason-box">
                <h5>Reason</h5>
                <p>{{ $systemStatus->status_reason }}</p>
            </div>
        @endif

        <p class="text-muted">
            Please try again later. If the problem persists, please contact the system administrator.
        </p>

        <a href="{{ route('login') }}" class="btn btn-primary mt-3">
            Back to Login
        </a>
    </div>
</body>
</html>
