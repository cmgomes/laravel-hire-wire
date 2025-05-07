<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function (){
    Route::get('account-types', [AccountTypeController::class, 'index']);
    Route::post('account/store', [AccountController::class, 'store'])->name('api.new-account.store');
    Route::post('deposit', [TransactionController::class, 'deposit'])->name('api.deposit');
    Route::post('withdraw', [TransactionController::class, 'withdraw'])->name('api.withdraw');
    Route::get('user/accounts', [AccountController::class, 'getUserAccounts'])->name('user.accounts');
    Route::get('transactions/last-week', [TransactionController::class, 'getLastWeekTransactions'])->name('api.transactions.last-week');
});
