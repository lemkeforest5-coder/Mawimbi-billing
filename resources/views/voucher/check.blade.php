<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Check Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .box {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 6px;
            padding: 15px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        input[type="text"] {
            width: 100%;
            padding: 6px 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: none;
            background: #3490dc;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
        .result {
            margin-top: 15px;
            font-size: 14px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .status-ok { color: green; }
        .status-bad { color: red; }
    </style>
</head>
<body>
<div class="box">
    <h1>Check Your Voucher</h1>

    <form method="POST" action="{{ route('voucher.check') }}">
        @csrf
        <label for="code">Voucher code</label>
        <input type="text"
               id="code"
               name="code"
               value="{{ old('code', $code ?? '') }}"
               placeholder="Enter your voucher code"
               autofocus>

        @error('code')
            <div style="color:red; font-size: 12px;">{{ $message }}</div>
        @enderror

        <button type="submit">Check</button>
    </form>

    @isset($voucher)
        <div class="result">
            @if ($voucher)
                <p>
                    Status:
                    @if ($voucher->status === 'new')
                        <span class="status-ok">Unused</span>
                    @elseif ($voucher->status === 'used')
                        <span class="status-bad">Used</span>
                    @elseif ($voucher->status === 'expired')
                        <span class="status-bad">Expired</span>
                    @else
                        <span>{{ $voucher->status }}</span>
                    @endif
                </p>

                <div class="row">
                    <span>Profile:</span>
                    <span>{{ $voucher->profile->name ?? '-' }}</span>
                </div>

                @php
                    $usedSeconds = $voucher->total_time_seconds ?? 0;
                    $remSeconds  = $voucher->remaining_time_seconds;
                @endphp

                @if(! ($usedSeconds === 0 && (! is_null($remSeconds) && $remSeconds === 0)))
                    <div class="row">
                        <span>Total time used:</span>
                        <span>{{ $voucher->total_time_human }}</span>
                    </div>
                @endif

                <div class="row">
                    <span>Remaining time:</span>
                    <span>
                        @if (! is_null($remSeconds))
                            @php
                                $h = intdiv($remSeconds, 3600);
                                $m = intdiv($remSeconds % 3600, 60);
                            @endphp
                            {{ sprintf('%02dh %02dm', $h, $m) }}
                        @else
                            Unlimited
                        @endif
                    </span>
                </div>

                <div class="row">
                    <span>Data used:</span>
                    <span>{{ number_format($voucher->total_data_mb ?? 0) }} MB</span>
                </div>

                <div class="row">
                    <span>Remaining data:</span>
                    <span>
                        @if (! is_null($voucher->remaining_data_mb))
                            {{ number_format($voucher->remaining_data_mb) }} MB
                        @else
                            Unlimited
                        @endif
                    </span>
                </div>

                <div class="row">
                    <span>Expires:</span>
                    <span>
                        @if ($voucher->expires_at)
                            {{ $voucher->expires_at->format('Y-m-d H:i') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            @else
                <p class="status-bad">
                    Voucher with code "{{ $code }}" was not found.
                </p>
            @endif
        </div>
    @endisset
</div>
</body>
</html>
