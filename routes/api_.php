<?php

use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('api/account-types', [AccountTypeController::class, 'index']);
    Route::post('api/account/store', [AccountController::class, 'store'])->name('api.new-account.store');
    Route::post('api/deposit', [TransactionController::class, 'deposit'])->name('api.deposit');
    Route::post('api/withdraw', [TransactionController::class, 'withdraw'])->name('api.withdraw');
    Route::get('api/user/accounts', [AccountController::class, 'getUserAccounts'])->name('user.accounts');
    Route::get('api/transactions/last-week', [TransactionController::class, 'getLastWeekTransactions'])->name('api.transactions.last-week');
});
