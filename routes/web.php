<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app_name' => config('app.name'),
        'laravel_stripe' => app()->version()
    ];
});

Route::middleware('guest')->group(function () {
    Route::post('/api/register', [RegisterController::class, 'store']);
    Route::post('/api/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->post('/api/logout', [LoginController::class, 'logout']);
