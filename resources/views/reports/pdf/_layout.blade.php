<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 4px 0; }
        .subtitle { color: #666; font-size: 11px; margin-bottom: 16px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 5px 8px; text-align: left; }
        th { background: #f3f4f6; border-bottom: 1px solid #999; }
        td { border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .font-mono { font-family: DejaVu Sans Mono, monospace; }
        .totals td { border-top: 2px solid #111; font-weight: bold; }
        .section-title { font-weight: bold; margin: 14px 0 4px 0; border-bottom: 1px solid #999; padding-bottom: 2px; }
        .footer { margin-top: 24px; color: #888; font-size: 9px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="subtitle">Smart Finance · Generated {{ now()->format('F d, Y g:ia') }}</div>

    @yield('content')

    <div class="footer">
        Parameters: {{ json_encode($parameters) }}
    </div>
</body>
</html>
