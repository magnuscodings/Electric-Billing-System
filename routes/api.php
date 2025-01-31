<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\MeterController;
use App\Http\Controllers\Api\MeterReadingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/incomingBillingRequestCount', [BillingController::class, 'getIncomingBillingRequestsCount']);

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::post('/forgot', [ForgotPasswordController::class, 'sendResetLinkEmailTest'])->name('password.email');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::get('/user', [AuthController::class, 'getCurrentUser']);
    Route::post('/change-password', [AuthController::class, 'apiChangePassword']);
    Route::post('/change-first-time-password', [AuthController::class, 'changeFirstTimePassword']);

    Route::apiResource('meter', MeterController::class);
    Route::get('meter/{meterId}/last-reading', [MeterReadingController::class, 'getLastReading']);
    Route::apiResource('meterReading', MeterReadingController::class);

    Route::get('client/me', [ClientController::class, 'showMyClient']);
    Route::get('/check-stall-number/{stallNumber}', [ClientController::class, 'checkStallNumber']);
    Route::get('/check-email/{email}', [ClientController::class, 'checkEmail']);

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    });
});
