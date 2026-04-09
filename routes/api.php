<?php
use App\Http\Controllers\Api\VoucherUsageController;
use App\Http\Controllers\MpesaPaymentController;
use App\Http\Controllers\MpesaCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoucherLoginController;
use App\Http\Controllers\Api\LoyaltyController;
use App\Http\Middleware\HotspotCors;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and will
| be assigned to the "api" middleware group. Make something great!
*/

Route::post('/mb/v1/voucher/login', VoucherLoginController::class)
    ->middleware(HotspotCors::class);

Route::get('/mb/v1/loyalty', [LoyaltyController::class, 'show']);
Route::post('/mpesa/callback', [MpesaCallbackController::class, 'handle']);
Route::post('mb/v1/pay/mpesa', [MpesaPaymentController::class, 'initiate']);
Route::post('/voucher/usage', VoucherUsageController::class)->name('api.voucher.usage');
