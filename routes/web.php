<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'app_name' => config('app.name'),
        'laravel_stripe' => app()->version()
    ];
});
