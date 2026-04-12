<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch voucher print</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
        }
        .card {
            border: 1px solid #000;
            width: 48%;
            box-sizing: border-box;
            padding: 8px;
            margin: 1%;
            height: 130px;
        }
        .code {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .small {
            font-size: 11px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="grid">
        @foreach($vouchers as $voucher)
            <div class="card">
                <div class="code">{{ $voucher->code }}</div>
                <div class="small">
                    Profile: {{ $voucher->profile->name ?? '-' }}<br>
                    Status: {{ $voucher->status }}<br>
                    Created: {{ $voucher->created_at }}<br>
                    @if($voucher->expires_at)
                        Expires: {{ $voucher->expires_at }}<br>
                    @endif
                </div>
                <div style="margin-top: 8px;" class="small">
                    Hotspot: connect to WiFi, open browser, enter this voucher code to go online.
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
