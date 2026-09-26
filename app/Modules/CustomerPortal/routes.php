<?php

use App\Modules\CustomerPortal\Controllers\CustomerPortalController;
use App\Modules\CustomerPortal\Controllers\DepositController;
use App\Modules\CustomerPortal\Controllers\InvoiceController;
use App\Modules\CustomerPortal\Controllers\TransactionHistoryController;
use App\Modules\CustomerPortal\Controllers\TransferController;
use App\Modules\CustomerPortal\Controllers\WithdrawController;
use App\Modules\CustomerPortal\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth'])->prefix('portal')->as('portal.')->group(function (): void {
        // Dashboard & Account Management
        Route::get('/', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
        Route::post('/accounts/create-secondary', [CustomerPortalController::class, 'createSecondaryAccount'])->name('accounts.create');

        // Deposit Money
        Route::get('/deposit', [DepositController::class, 'create'])->name('deposit');
        Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');

        // Withdraw Money
        Route::get('/withdraw', [WithdrawController::class, 'create'])->name('withdraw');
        Route::post('/withdraw', [WithdrawController::class, 'store'])->name('withdraw.store');

        // Transfer Money
        Route::get('/transfer', [TransferController::class, 'create'])->name('transfer');
        Route::post('/transfer', [TransferController::class, 'store'])->name('transfer.store');

        // Invoices & Utility Bills
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
        Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        Route::post('/invoices/pay-custom', [InvoiceController::class, 'payCustom'])->name('invoices.pay-custom');

        // Transaction History & Receipts
        Route::get('/transactions', [TransactionHistoryController::class, 'index'])->name('transactions');
        Route::get('/transactions/{id}/receipt', [TransactionHistoryController::class, 'receipt'])->name('transactions.receipt');

        // Dedicated Two-Factor Authentication for Customer
        Route::get('/security/2fa', [TwoFactorController::class, 'index'])->name('2fa');
        Route::post('/security/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::delete('/security/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::post('/security/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.recovery-codes');
    });
});
