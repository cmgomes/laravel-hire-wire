<?php

use App\Services\AccountService;
use Carbon\Carbon;

Schedule::call(function () {
    $accService = new AccountService();
    $accService->applyMonthlyAdjustment();
})->monthlyOn(6, '23:59');
