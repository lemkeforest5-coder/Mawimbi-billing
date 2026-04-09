<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherUsageController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('Voucher usage hit', $request->all());

        $data = $request->validate([
            'username'     => 'required|string',   // voucher code
            'session_time' => 'nullable|numeric',  // seconds in this session
            'session_data' => 'nullable|numeric',  // bytes in this session
        ]);

        $voucher = Voucher::where('code', $data['username'])->first();

        if (! $voucher) {
            return response()->json([
                'ok'      => false,
                'reason'  => 'not_found',
                'message' => 'Voucher not found.',
            ], 404);
        }

             $rawTime = $data['session_time'] ?? null;
        $sessionTime = 0;

        if ($rawTime) {
            // parse formats like "10s", "5m30s", "2h10m5s"
            if (preg_match('/(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/', $rawTime, $m)) {
                $hours   = isset($m[1]) ? (int) $m[1] : 0;
                $minutes = isset($m[2]) ? (int) $m[2] : 0;
                $seconds = isset($m[3]) ? (int) $m[3] : 0;
                $sessionTime = $hours * 3600 + $minutes * 60 + $seconds;
            }
        }

        $sessionDataBytes = (int) ($data['session_data'] ?? 0);

        if ($sessionTime > 0) {
            $voucher->total_time_seconds = ($voucher->total_time_seconds ?? 0) + $sessionTime;
        }

        if ($sessionDataBytes > 0) {
            $sessionDataMb = $sessionDataBytes / (1024 * 1024);
            $voucher->total_data_mb = ($voucher->total_data_mb ?? 0) + $sessionDataMb;
        }

        $voucher->save();

        return response()->json([
            'ok'              => true,
            'message'         => 'Usage updated.',
            'total_time_sec'  => $voucher->total_time_seconds,
            'total_data_mb'   => $voucher->total_data_mb,
        ]);
    }
}
