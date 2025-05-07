<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NewAccountController;
use App\Http\Controllers\DepositController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'appUrl' => URL::to('/')
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('deposit', function () {
    return Inertia::render('Deposit');
})->middleware(['auth', 'verified'])->name('deposit');

Route::get('new-account', function () {
    return Inertia::render('NewAccount');
})->middleware(['auth', 'verified'])->name('nova.conta');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
//require __DIR__ . '/api_.php';
