<!DOCTYPE html>
<html lang="{{ $locale ?? 'de' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Interdiscount' }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f5a623; padding: 24px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { background-color: #ffffff; padding: 32px; border-left: 1px solid #e4e4e7; border-right: 1px solid #e4e4e7; }
        .content h2 { color: #18181b; font-size: 20px; margin-top: 0; }
        .content p { color: #3f3f46; line-height: 1.6; margin: 12px 0; }
        .btn { display: inline-block; background-color: #f5a623; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 16px 0; }
        .footer { background-color: #fafafa; padding: 20px 32px; text-align: center; border-radius: 0 0 8px 8px; border: 1px solid #e4e4e7; border-top: none; }
        .footer p { color: #71717a; font-size: 12px; margin: 4px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .info-table td { padding: 8px 12px; border-bottom: 1px solid #e4e4e7; color: #3f3f46; }
        .info-table td:first-child { font-weight: 600; color: #18181b; width: 40%; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Interdiscount</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Interdiscount Clone. All rights reserved.</p>
            <p>This is an automated message. Please do not reply directly.</p>
        </div>
    </div>
</body>
</html>
