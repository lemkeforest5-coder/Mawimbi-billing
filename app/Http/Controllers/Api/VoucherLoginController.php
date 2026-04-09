<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotspotUser;
use App\Models\Router;
use App\Models\Voucher;
use App\Services\MikrotikService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherLoginController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function __invoke(Request $request)
    {
        try {
            Log::info('Voucher login hit', $request->all());

            $data = $request->validate([
                'voucher_code' => 'required|string',
                'router_id'    => 'nullable|integer|exists:routers,id',
                'mac_address'  => 'nullable|string|max:64',
                'ip_address'   => 'nullable|string|max:64',
                'phone'        => 'nullable|string|max:32',
            ]);

            // 1. Load voucher
            $voucher = Voucher::with(['profile'])
                ->where('code', $data['voucher_code'])
                ->first();

            if (! $voucher) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'not_found',
                    'message' => 'Voucher not found.',
                ], 404);
            }

            // 1b. Time/data limit checks
            if ($voucher->time_limit_seconds !== null &&
                $voucher->total_time_seconds >= $voucher->time_limit_seconds) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'limit_reached',
                    'message' => 'Voucher time limit reached.',
                ], 422);
            }

            if ($voucher->data_limit_mb !== null &&
                $voucher->total_data_mb >= $voucher->data_limit_mb) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'limit_reached',
                    'message' => 'Voucher data limit reached.',
                ], 422);
            }

            // 2. Basic status checks
            if ($voucher->status === 'blocked') {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'blocked',
                    'message' => 'This voucher is blocked.',
                ], 422);
            }

            if ($voucher->status === 'used') {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'used',
                    'message' => 'Voucher has already been used.',
                ], 422);
            }

            if ($voucher->valid_until && $voucher->valid_until->isPast()) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'expired',
                    'message' => 'Voucher has expired.',
                ], 422);
            }

            // 3. Router resolution
            if (! empty($data['router_id'])) {
                $router = Router::find($data['router_id']);
            } else {
                $router = $voucher->profile->router ?? null;
            }

            if (! $router) {
                return response()->json([
                    'ok'      => false,
                    'reason'  => 'no_router',
                    'message' => 'No router configured for this voucher.',
                ], 500);
            }

            // 4. Device / MAC handling (simplified for now)
            $mac = $data['mac_address'] ?? null;

            if ($mac && $voucher->max_devices !== null && $voucher->max_devices > 0) {
                $usedDevices = HotspotUser::where('voucher_id', $voucher->id)
                    ->whereNotNull('mac_address')
                    ->distinct('mac_address')
                    ->count('mac_address');

                if ($usedDevices >= $voucher->max_devices) {
                    return response()->json([
                        'ok'      => false,
                        'reason'  => 'device_limit',
                        'message' => 'Voucher device limit reached.',
                    ], 422);
                }
            }

            // 5. Create/update local HotspotUser model
            $hotspotUser = $voucher->hotspotUser ?: new HotspotUser();
            $hotspotUser->router_id     = $router->id;
            $hotspotUser->profile_id    = $voucher->profile_id;
            $hotspotUser->username      = $voucher->code;
            $hotspotUser->password      = $voucher->code;
            $hotspotUser->last_login_at = Carbon::now();
            $hotspotUser->save();

            // 6. Mark voucher as used
            $voucher->status          = 'used';
            $voucher->used_at         = Carbon::now();
            $voucher->customer_phone  = $data['phone'] ?? $voucher->customer_phone;
            $voucher->hotspot_user_id = $hotspotUser->id;
            $voucher->save();

            // 7. Push user to MikroTik
            $profile     = $voucher->profile;
            $router      = $profile->router ?? $router;
            $profileName = $profile->router_profile_name ?? $profile->name;

            $username = $voucher->code;
            $password = $voucher->code;

            Log::info('Voucher accepted, calling Mikrotik', [
                'voucher_id'   => $voucher->id,
                'code'         => $voucher->code,
                'router_id'    => $router->id ?? null,
                'profile_name' => $profileName,
            ]);

            try {
                $this->mikrotik->createOrEnableHotspotUser(
                    $router,
                    $username,
                    $profileName
                );
            } catch (\Throwable $e) {
                Log::error('Mikrotik voucher login error: '.$e->getMessage(), [
                    'voucher_id' => $voucher->id,
                    'router_id'  => $router->id ?? null,
                ]);
            }

            // 8. Response to hotspot
            $plan     = $voucher->profile;
            $planCode = $plan->name ?? null;

            return response()->json([
                'ok'          => true,
                'message'     => 'Voucher accepted.',
                'mk_user'     => $username,
                'mk_pass'     => $password,
                'plan'        => $planCode,
                'valid_until' => $voucher->valid_until,
            ]);
        } catch (\Throwable $e) {
            Log::error('Voucher login fatal error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok'      => false,
                'reason'  => 'server_error',
                'message' => 'Internal server error on voucher login.',
            ], 500);
        }
    }
}
