<?php

use App\Http\Controllers\MyFirstController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "AAAAAAAAAAAAAAAAAAAA";
});

Route::get('/firstpage', [MyFirstController::class, 'index']);
