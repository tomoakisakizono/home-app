<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// ユーザ登録
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// ログイン
Route::get('/', [LoginController::class, 'showForm'])->name('login.form');
Route::post('/', [LoginController::class, 'login'])->name('login.store');
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// トップページ表示
Route::prefix('users/{id}')->group(function () {
    Route::get('/', [UsersController::class, 'index'])->name('users.show');
});
