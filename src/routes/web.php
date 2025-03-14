<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PairController;

// ユーザ登録
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// ログイン
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// トップページ表示
// Route::prefix('users/{id}')->group(function () {
//     Route::get('/', [UsersController::class, 'index'])->name('users.show');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/pair', [PairController::class, 'show'])->name('pair.show');
    Route::get('/pair/setup', [PairController::class, 'setup'])->name('pair.setup');
    Route::post('/pair/invite', [PairController::class, 'invite'])->name('pair.invite');
    Route::post('/pair/accept', [PairController::class, 'accept'])->name('pair.accept');
    Route::post('/pair/decline/{pair_id}', [PairController::class, 'decline'])->name('pair.decline');
});