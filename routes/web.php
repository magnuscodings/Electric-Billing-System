<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Custom forgot password route if you need to override
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/invoicetemplate', function () {
        return view('invoicetemplate'); //
    });

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('view.dashboard');

        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'Clients'])->name('view.clients');
            Route::post('/', [ClientController::class, 'Store'])->name('view.client.store');
            Route::get('/search', [ClientController::class, 'search'])->name('view.client.search');
            Route::get('/{id}/billings/', [ClientController::class, 'billings'])->name('view.client.billings');
            Route::delete('/{id}', [ClientController::class, 'destroy'])->name('view.client.destroy');
            Route::get('/{id}/report/', [ClientController::class, 'printReport'])->name('view.client.report');
        });

        Route::get('/api/clients/{id}', [ClientController::class, 'show'])->name('view.client.show');
        Route::post('/api/clients/{id}', [ClientController::class, 'update'])->name('view.client.update');
        Route::post('/view/client/{id}/update', [ClientController::class, 'update'])->name('view.client.update');

        Route::post('/meter/validate-code', [MeterController::class, 'validateMeterCode']);

        Route::prefix('meters')->group(function () {
            Route::get('/', [MeterController::class, 'Meters'])->name('view.meters');
            Route::post('/', [MeterController::class, 'Store'])->name('view.meter.store');
            Route::get('/search', [MeterController::class, 'search'])->name('view.meter.search');
            Route::delete('/{id}', [MeterController::class, 'destroy'])->name('view.meter.destroy');
            Route::get('/{id}', [MeterController::class, 'show'])->name('view.meter.show');
        });

        Route::prefix('billings')->group(function () {
            Route::get('/', [BillingController::class, 'Billings'])->name('view.billings');
            Route::post('/', [BillingController::class, 'Store'])->name('view.billing.store');
        });

        Route::prefix('pendings')->group(function () {
            Route::get('/', [BillingController::class, 'PendingBillings'])->name('view.pendings');
            Route::post('/{id}', [BillingController::class, 'markAsPaid'])->name('view.pending.markAsPaid');
        });

        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('view.reports');
            Route::get('/monthly', [ReportController::class, 'monthlyReport'])->name('view.report.monthly');
        });

        Route::prefix('accounts')->group(function () {
            Route::get('/', [AuthController::class, 'showAccounts'])->name('view.accounts');
            Route::post('/', [AuthController::class, 'register'])->name('view.account');
            Route::put('/{id}', [AuthController::class, 'update'])->name('view.account.update');
            Route::delete('/{id}', [AuthController::class, 'destroy'])->name('view.account.destroy');
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('view.profile');
            Route::put('/update', [ProfileController::class, 'update'])->name('view.profile.update');
            Route::put('/updatePassword', [ProfileController::class, 'updatePassword'])->name('view.profile.updatePassword');
        });
    });
});

Route::get('/sample', function () {
    return view('layout.sample');
});
