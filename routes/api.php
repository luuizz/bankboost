<?php

use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DepositController;
use App\Http\Controllers\Api\V1\PixController;
use App\Http\Controllers\Api\V1\PixKeyController;
use App\Http\Controllers\Api\V1\StatementController;
use App\Http\Controllers\Api\V1\TransferController;
use App\Http\Controllers\Api\V1\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::middleware('throttle:auth')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::middleware(['auth:sanctum', 'throttle:banking'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('account', [AccountController::class, 'show'])->name('account.show');
        Route::get('statement', [StatementController::class, 'index'])->name('statement.index');

        Route::post('deposits', [DepositController::class, 'store'])->name('deposits.store');
        Route::post('withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

        Route::post('transfers', [TransferController::class, 'store'])->name('transfers.store');
        Route::get('transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');

        Route::get('pix/keys', [PixKeyController::class, 'index'])->name('pix.keys.index');
        Route::post('pix/keys', [PixKeyController::class, 'store'])->name('pix.keys.store');
        Route::delete('pix/keys/{pixKey}', [PixKeyController::class, 'destroy'])->name('pix.keys.destroy');
        Route::get('pix/lookup', [PixController::class, 'lookup'])->name('pix.lookup');
        Route::post('pix/transfers', [PixController::class, 'store'])->name('pix.transfers.store');
    });
});
