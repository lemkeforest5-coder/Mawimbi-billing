<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return response()->json([
        'app' => 'Mawimbi Billing',
        'status' => 'ok',
        'time' => now()->toDateTimeString(),
    ]);
});

Route::get('/hotspot', function () {
    return view('hotspot');
});

// Routers
Route::resource('routers', RouterController::class)->except(['show']);
// Profiles
Route::resource('profiles', ProfileController::class)->except(['show']);

// Vouchers
Route::resource('vouchers', VoucherController::class)->except(['show']);

// Manual send voucher to Mikrotik
Route::post(
    '/vouchers/{voucher}/send-to-mikrotik',
    [VoucherController::class, 'sendToMikrotik']
)->name('vouchers.sendToMikrotik');

// Payments list + create + store
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
