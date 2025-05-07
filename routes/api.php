<?php

use App\Http\Middleware\PreventInertiaOnApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

Route::middleware(PreventInertiaOnApi::class)->group(function () {
    Route::post('login', [AuthController::class, 'loginPassport']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware(['auth:api', PreventInertiaOnApi::class])->group(function (){
    Route::get('account-types', [AccountTypeController::class, 'index']);
    Route::post('account/store', [AccountController::class, 'store'])->name('api.new-account.store');
    Route::post('deposit', [TransactionController::class, 'deposit'])->name('api.deposit');
    Route::post('withdraw', [TransactionController::class, 'withdraw'])->name('api.withdraw');
    Route::get('user/accounts', [AccountController::class, 'getUserAccounts'])->name('user.accounts');
    Route::get('transactions/last-week', [TransactionController::class, 'getLastWeekTransactions'])->name('api.transactions.last-week');
});
