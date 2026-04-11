<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherPortalController extends Controller
{
    public function showForm()
    {
        return view('voucher.check');
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        $voucher = Voucher::with('profile')->where('code', $data['code'])->first();

        return view('voucher.check', [
            'voucher' => $voucher,
            'code'    => $data['code'],
        ]);
    }
}
