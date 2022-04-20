<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::post('/forgot-password', 'store');
        Route::post('/forgot-password/reset', 'update');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payment-method', PaymentMethodController::class);
    Route::post('/reset-password', [ResetPasswordController::class, 'store']);
});
