<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Router;
use App\Models\Profile;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function index(Request $request)
    {
        $query = Voucher::with(['router', 'profile'])
            ->withCount('payments')
            ->orderByDesc('id');

        // Text search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Profile filter (for per-profile drilldown)
        if ($profileId = $request->input('profile_id')) {
            $query->where('profile_id', $profileId);
        }

        // Date range filter (created_at) coming from admin overview
        if ($range = $request->input('range')) {
            switch ($range) {
                case '7':
                    $from = now()->startOfDay()->subDays(6);
                    $to   = now()->endOfDay();
                    break;
                case '30':
                    $from = now()->startOfDay()->subDays(29);
                    $to   = now()->endOfDay();
                    break;
                case 'all':
                    $from = null;
                    $to   = null;
                    break;
                case 'today':
                default:
                    $from = now()->startOfDay();
                    $to   = now()->endOfDay();
                    break;
            }

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        $vouchers = $query->paginate(25)->withQueryString();

        return view('vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $routers  = Router::where('enabled', true)->orderBy('name')->get();
        $profiles = Profile::orderBy('name')->get();

        return view('vouchers.create', compact('routers', 'profiles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'router_id'  => 'required|exists:routers,id',
            'profile_id' => 'required|exists:profiles,id',
            'code'       => 'nullable|string|max:64',
            'expires_at' => 'nullable|date',
        ]);

        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(8));
        }

        $data['status'] = 'new';

        // Pull limits from the selected profile
        $profile = Profile::find($data['profile_id']);

        if ($profile) {
            if (! is_null($profile->time_limit_minutes)) {
                $data['time_limit_seconds'] = $profile->time_limit_minutes * 60;
            }

            if (! is_null($profile->data_limit_mb)) {
                $data['data_limit_mb'] = $profile->data_limit_mb;
            }
        }

        Voucher::create($data);

        return redirect()
            ->route('vouchers.index')
            ->with('status', 'Voucher created.');
    }

    public function edit(Voucher $voucher)
    {
        $routers  = Router::where('enabled', true)->orderBy('name')->get();
        $profiles = Profile::orderBy('name')->get();

        return view('vouchers.edit', compact('voucher', 'routers', 'profiles'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'router_id'  => 'required|exists:routers,id',
            'profile_id' => 'required|exists:profiles,id',
            'code'       => 'required|string|max:64',
            'status'     => 'required|string|max:32',
            'expires_at' => 'nullable|date',
        ]);

        $voucher->update($data);

        return redirect()
            ->route('vouchers.index')
            ->with('status', 'Voucher updated.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()
            ->route('vouchers.index')
            ->with('status', 'Voucher deleted.');
    }

    public function sendToMikrotik(Voucher $voucher)
    {
        $router = $voucher->router;

        if (! $router) {
            return back()->with('status', 'No router attached to this voucher.');
        }

        $profile = $voucher->profile;
        $routerProfileName = $profile->router_profile_name ?? $profile->name ?? 'default';

        try {
            $this->mikrotik->createOrEnableHotspotUser(
                $router,
                $voucher->code,
                $routerProfileName
            );

            $voucher->update(['synced_to_mikrotik' => true]);

            return back()->with('status', "Voucher {$voucher->code} sent to Mikrotik.");
        } catch (\Throwable $e) {
            \Log::error('SendToMikrotik failed', [
                'voucher_id' => $voucher->id,
                'error'      => $e->getMessage(),
            ]);

            return back()->with('status', 'Failed to send to Mikrotik: '.$e->getMessage());
        }
    }

    public function print(Voucher $voucher)
    {
        return view('vouchers.print', compact('voucher'));
    }
}
