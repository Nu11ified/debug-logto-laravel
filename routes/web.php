<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/callback', [AuthController::class, 'callback']);
Route::get('/sign-in', [AuthController::class, 'signIn']);
Route::get('/sign-out', [AuthController::class, 'signOut']);
Route::get('/', [AuthController::class, 'home']);
Route::get('/userinfo', [AuthController::class, 'userInfo']);
