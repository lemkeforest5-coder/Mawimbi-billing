<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminOverviewController;
use App\Http\Controllers\VoucherPortalController;

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

Route::get('/voucher/check', [VoucherPortalController::class, 'showForm'])
    ->name('voucher.check.form');

Route::post('/voucher/check', [VoucherPortalController::class, 'check'])
    ->name('voucher.check');

// Admin overview
Route::get('/admin/overview', [AdminOverviewController::class, 'index'])
    ->name('admin.overview');

// Routers
Route::resource('routers', RouterController::class)->except(['show']);

// Profiles
Route::resource('profiles', ProfileController::class)->except(['show']);

// Vouchers
Route::resource('vouchers', VoucherController::class)->except(['show']);

Route::get('/vouchers/{voucher}/print', [VoucherController::class, 'print'])
    ->name('vouchers.print');

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
