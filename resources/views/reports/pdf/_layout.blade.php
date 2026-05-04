<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; margin: 0; }
        .report-heading { background: #6366f1; color: #fff; text-align: center; padding: 18px 12px; margin: 0 0 18px 0; }
        .report-heading .company { font-size: 16px; font-weight: bold; letter-spacing: 0.5px; }
        .report-heading .statement { font-size: 12px; margin-top: 4px; }
        .report-heading .period { font-size: 10px; margin-top: 3px; color: #e0e7ff; }
        .body-pad { padding: 0 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 3px 0; text-align: left; vertical-align: top; }
        .text-right { text-align: right; }
        .font-mono { font-family: DejaVu Sans Mono, monospace; }
        .total-amount-header { text-align: right; font-weight: bold; border-bottom: 1px solid #444; padding-bottom: 4px; margin-bottom: 6px; }
        .section-label { color: #6366f1; font-weight: bold; font-size: 13px; margin: 14px 0 4px 0; }
        .group-header { font-weight: bold; padding: 4px 0; }
        .group-row td { padding: 2px 0; }
        .indent-1 { padding-left: 12px !important; }
        .indent-2 { padding-left: 28px !important; }
        .single-rule { border-top: 1px solid #111; padding-top: 2px !important; }
        .double-rule { border-top: 1px solid #111; border-bottom: 3px double #111; padding: 2px 0 !important; }
        .grand-total td { font-weight: bold; padding-top: 6px; }
        .footer { margin: 24px 24px 0 24px; color: #888; font-size: 9px; }
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

    <div class="body-pad">
        @yield('content')
    </div>

    <div class="footer">
        Generated {{ now()->format('F j, Y g:ia') }} · Parameters: {{ json_encode($parameters) }}
    </div>
</body>
</html>
