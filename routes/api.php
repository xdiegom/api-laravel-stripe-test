<?php

use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;

Route::controller(StripeController::class)->group(function () {
    Route::post('pay', 'pay');
});
