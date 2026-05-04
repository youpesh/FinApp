<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .report-heading { text-align: center; margin: 0 0 18px 0; padding-bottom: 8px; border-bottom: 1px solid #999; }
        .report-heading .company { font-size: 16px; font-weight: bold; letter-spacing: 0.5px; }
        .report-heading .statement { font-size: 13px; font-weight: bold; margin-top: 2px; }
        .report-heading .period { font-size: 11px; color: #555; margin-top: 2px; }
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
    <div class="report-heading">
        <div class="company">{{ config('app.company_name') }}</div>
        <div class="statement">{{ $statementTitle ?? $title }}</div>
        @isset($periodLabel)
            <div class="period">{{ $periodLabel }}</div>
        @endisset
    </div>

    @yield('content')

    <div class="footer">
        Generated {{ now()->format('F d, Y g:ia') }} · Parameters: {{ json_encode($parameters) }}
    </div>
</body>
</html>
