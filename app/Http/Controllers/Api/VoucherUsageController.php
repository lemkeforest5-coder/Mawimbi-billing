<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherUsageController extends Controller
{
    public function __invoke(Request $request)
    {
        // Uncomment this while debugging to see what Mikrotik sends:
        // dd($request->all());

        Log::info('Voucher usage hit', $request->all());

        $data = $request->validate([
            'username'        => 'required|string',   // voucher code / hotspot username
            'session_time'    => 'nullable|string',   // Mikrotik uptime, ideally seconds (e.g. "600")
            'session_data'    => 'nullable|numeric',  // bytes in this session (from $bytes-out)
            'hotspot_user_id' => 'nullable|string',
        ]);

        // 1) Find the voucher by code
        $voucher = Voucher::where('code', $data['username'])->first();

        if (! $voucher) {
            return response()->json([
                'ok'      => false,
                'reason'  => 'not_found',
                'message' => 'Voucher not found.',
            ], 404);
        }

        // 2) Parse session time
        $rawTime = $data['session_time'] ?? null;
        $sessionTime = 0;

        if (! empty($rawTime)) {
            // Prefer: send $uptime-secs from Mikrotik so this is just seconds.
            // If it's purely digits, treat as seconds directly.
            if (ctype_digit((string) $rawTime)) {
                $sessionTime = (int) $rawTime;
            } else {
                // Fallback: parse formats like "10s", "5m30s", "2h10m5s"
                if (preg_match('/(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/', $rawTime, $m)) {
                    $hours   = isset($m[1]) ? (int) $m[1] : 0;
                    $minutes = isset($m[2]) ? (int) $m[2] : 0;
                    $seconds = isset($m[3]) ? (int) $m[3] : 0;

                    $sessionTime = $hours * 3600 + $minutes * 60 + $seconds;
                }
            }
        }

        // 3) Parse session data (bytes -> MB)
        $sessionDataBytes = (int) ($data['session_data'] ?? 0);

        // 4) Accumulate usage
        if ($sessionTime > 0) {
            $voucher->total_time_seconds = ($voucher->total_time_seconds ?? 0) + $sessionTime;
        }

        if ($sessionDataBytes > 0) {
            $sessionDataMb = $sessionDataBytes / (1024 * 1024);
            $voucher->total_data_mb = ($voucher->total_data_mb ?? 0) + $sessionDataMb;
        }

        // 5) First usage: mark used + set used_at
        if (is_null($voucher->used_at)) {
            $voucher->used_at = now();

            if ($voucher->status !== 'used') {
                $voucher->status = 'used';
            }

            Log::info('Voucher first usage, marking used_at', [
                'voucher_id' => $voucher->id,
                'code'       => $voucher->code,
            ]);
        }

        // 6) Optional: store Mikrotik user id if provided
        if (! empty($data['hotspot_user_id'])) {
            $voucher->hotspot_user_id = $data['hotspot_user_id'];
        }

        // 7) Enforce time/data limits based on profile
        $profile = $voucher->profile;

        if ($profile) {
            $limitSeconds = $profile->time_limit_minutes
                ? $profile->time_limit_minutes * 60
                : null;

            $limitMb = $profile->data_limit_mb ?: null;

            $timeExceeded = $limitSeconds !== null
                && ($voucher->total_time_seconds ?? 0) >= $limitSeconds;

            $dataExceeded = $limitMb !== null
                && ($voucher->total_data_mb ?? 0) >= $limitMb;

            if ($timeExceeded || $dataExceeded) {
                // For now keep status as 'used'; you can introduce 'expired' later
                $voucher->status = 'used';

                Log::info('Voucher limits reached, marking as used', [
                    'voucher_id'          => $voucher->id,
                    'code'                => $voucher->code,
                    'time_limit_seconds'  => $limitSeconds,
                    'total_time_seconds'  => $voucher->total_time_seconds,
                    'data_limit_mb'       => $limitMb,
                    'total_data_mb'       => $voucher->total_data_mb,
                    'time_exceeded'       => $timeExceeded,
                    'data_exceeded'       => $dataExceeded,
                ]);
            }
        }

        $voucher->save();

        return response()->json([
            'ok'             => true,
            'message'        => 'Usage updated.',
            'status'         => $voucher->status,
            'used_at'        => optional($voucher->used_at)->toDateTimeString(),
            'total_time_sec' => $voucher->total_time_seconds,
            'total_data_mb'  => $voucher->total_data_mb,
        ]);
    }
}
