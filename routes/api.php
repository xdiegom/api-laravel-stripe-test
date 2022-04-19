<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::controller(CheckoutController::class)->group(function () {
    Route::post('pay', 'pay');
});


Route::middleware('guest')->group(function () {
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::post('/forgot-password', 'store');
        Route::post('/forgot-password/reset', 'update');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reset-password', [ResetPasswordController::class, 'store']);
});
