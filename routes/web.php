<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\MovementController;
use App\Http\Controllers\Panel\PixController;
use App\Http\Controllers\Panel\StatementController;
use App\Http\Controllers\Panel\TransferController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\CareersController;
use App\Http\Controllers\Site\HelpController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Site institucional
*/
Route::get('/', HomeController::class)->name('home');

Route::name('site.')->group(function () {
    Route::view('conta-digital', 'site.pages.account')->name('account');
    Route::view('cartao', 'site.pages.card')->name('card');
    Route::view('investimentos', 'site.pages.investments')->name('investments');
    Route::view('emprestimo', 'site.pages.loans')->name('loans');
    Route::view('conta-pj', 'site.pages.business')->name('business');
    Route::view('quem-somos', 'site.pages.about')->name('about');
    Route::get('carreiras', CareersController::class)->name('careers');

    Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/{post}', [BlogController::class, 'show'])->name('blog.show');

    Route::get('ajuda', [HelpController::class, 'index'])->name('help');
    Route::post('ajuda/contato', [HelpController::class, 'contact'])->middleware('throttle:contact')->name('help.contact');

    Route::view('termos', 'site.pages.legal', ['page' => 'terms'])->name('terms');
    Route::view('privacidade', 'site.pages.legal', ['page' => 'privacy'])->name('privacy');
});

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

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
