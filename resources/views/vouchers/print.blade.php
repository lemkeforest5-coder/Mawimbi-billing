<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voucher {{ $voucher->code }}</title>
    <style>
        @media print {
            @page { size: A4; margin: 10mm; }
            body { margin: 0; }
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .page {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .voucher-card {
            width: 8cm;
            border: 1px solid #333;
            border-radius: 6px;
            background: #fff;
            padding: 8px 10px;
            box-sizing: border-box;
        }
        .voucher-header {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin-bottom: 6px;
        }
        .voucher-code {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
            margin: 6px 0;
        }
        .voucher-row {
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .small {
            font-size: 10px;
            margin-top: 6px;
            text-align: center;
        }
        .actions {
            margin: 10px 0;
            text-align: right;
        }
        .btn-print {
            padding: 6px 10px;
            background: #3490dc;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        @media print {
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="actions">
    <button class="btn-print" onclick="window.print()">Print</button>
</div>

<div class="page">
    <div class="voucher-card">
        <div class="voucher-header">
            Mawimbi Hotspot
        </div>

        <div class="voucher-code">
            {{ $voucher->code }}
        </div>

        <div class="voucher-row">
            <span>Profile:</span>
            <span>{{ $voucher->profile->name ?? '-' }}</span>
        </div>

        <div class="voucher-row">
            <span>Time:</span>
            <span>
                @if($voucher->profile && $voucher->profile->time_limit_minutes)
                    {{ $voucher->profile->time_limit_minutes }} min
                @else
                    Unlimited
                @endif
            </span>
        </div>

        <div class="voucher-row">
            <span>Data:</span>
            <span>
                @if($voucher->profile && $voucher->profile->data_limit_mb)
                    {{ number_format($voucher->profile->data_limit_mb) }} MB
                @else
                    Unlimited
                @endif
            </span>
        </div>

        <div class="voucher-row">
            <span>Price:</span>
            <span>{{ number_format($voucher->price) }} KES</span>
        </div>

        <div class="voucher-row">
            <span>Expires:</span>
            <span>{{ $voucher->expires_at ? $voucher->expires_at->format('Y-m-d') : '-' }}</span>
        </div>

        <div class="small">
            Connect to SSID: <strong>Mawimbi WiFi</strong><br>
            Open browser, enter voucher code when asked.<br>
            Check balance at: <strong>hotspot.local/voucher/check</strong>
        </div>
    </div>
</div>

</body>
</html>
