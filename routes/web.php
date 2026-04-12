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

// Admin login – handled entirely by AdminGate middleware
Route::match(['get', 'post'], '/admin/login', function () {
    // This will never be executed; AdminGate returns the response.
})->middleware('admin')->name('admin.login');

Route::middleware('admin')->group(function () {

    // Simple logout: clear admin flag and go back to login
    Route::get('/admin/logout', function (\Illuminate\Http\Request $request) {
        $request->session()->forget('is_admin');
        return redirect('/admin/login');
    })->name('admin.logout');

    // Admin overview
    Route::get('/admin/overview', [AdminOverviewController::class, 'index'])
        ->name('admin.overview');

    // Routers
    Route::resource('routers', RouterController::class)->except(['show']);

    // Profiles
    Route::resource('profiles', ProfileController::class)->except(['show']);

    // Batch voucher printing – must come BEFORE resource route
    Route::get('/vouchers/batch/print', [VoucherController::class, 'batchPrintForm'])
        ->name('vouchers.batch.print.form');

    Route::post('/vouchers/batch/print', [VoucherController::class, 'batchPrint'])
        ->name('vouchers.batch.print');

    // Vouchers
    Route::resource('vouchers', VoucherController::class)->except(['show']);

    Route::get('/vouchers/{voucher}/print', [VoucherController::class, 'print'])
        ->name('vouchers.print');

    Route::get('/vouchers/{voucher}', [VoucherController::class, 'show'])
        ->name('vouchers.show');

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
});
