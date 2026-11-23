<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\MovementController;
use App\Http\Controllers\Panel\PixController;
use App\Http\Controllers\Panel\StatementController;
use App\Http\Controllers\Panel\TransferController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.index')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('entrar', [SessionController::class, 'create'])->name('login');
    Route::post('entrar', [SessionController::class, 'store'])->middleware('throttle:auth');

    Route::get('abrir-conta', [RegisterController::class, 'create'])->name('register');
    Route::post('abrir-conta', [RegisterController::class, 'store'])->middleware('throttle:auth');
});

Route::middleware('auth')->group(function () {
    Route::post('sair', [SessionController::class, 'destroy'])->name('logout');

    Route::prefix('painel')->name('panel.')->middleware('throttle:banking')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('extrato', StatementController::class)->name('statement');

        Route::post('depositos', [MovementController::class, 'deposit'])->name('deposits.store');
        Route::post('saques', [MovementController::class, 'withdraw'])->name('withdrawals.store');

        Route::get('transferir', [TransferController::class, 'create'])->name('transfers.create');
        Route::post('transferir', [TransferController::class, 'store'])->name('transfers.store');
        Route::get('comprovantes/{transfer}', [TransferController::class, 'show'])->name('receipts.show');

        Route::get('pix', [PixController::class, 'index'])->name('pix');
        Route::get('pix/consulta', [PixController::class, 'lookup'])->name('pix.lookup');
        Route::post('pix/enviar', [PixController::class, 'send'])->name('pix.send');
        Route::post('pix/chaves', [PixController::class, 'storeKey'])->name('pix.keys.store');
        Route::delete('pix/chaves/{pixKey}', [PixController::class, 'destroyKey'])->name('pix.keys.destroy');
    });
});
