<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::controller(CheckoutController::class)->group(function () {
    Route::post('pay', 'pay');
});
