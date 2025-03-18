<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PairController;
use App\Http\Controllers\FunctionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\CategoryController;

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
    Route::get('/pair/edit', [PairController::class, 'edit'])->name('pair.edit');
    Route::post('/pair/update_image', [PairController::class, 'updateImage'])->name('pair.update_image');
    Route::post('/pair/update_name', [PairController::class, 'updateName'])->name('pair.update_name');
    Route::get('/pair/functions', [FunctionController::class, 'index'])->name('pair.functions');
    Route::post('/pair/functions/store', [FunctionController::class, 'store'])->name('pair.functions.store');
    Route::post('/pair/decline/{pair_id}', [PairController::class, 'decline'])->name('pair.decline');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index'); // メッセージ一覧
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store'); // メッセージ投稿
    Route::get('/messages/{id}/edit', [MessageController::class, 'edit'])->name('messages.edit'); // 編集ページ
    Route::put('/messages/{id}', [MessageController::class, 'update'])->name('messages.update'); // メッセージ更新
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy'); // メッセージ削除

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index'); // カレンダー表示
    Route::post('/calendar/store', [CalendarController::class, 'store'])->name('calendar.store'); // 予定登録
    Route::get('/calendar/{id}', [CalendarController::class, 'show'])->name('calendar.show'); // 予定詳細
    Route::get('/calendar/{id}/edit', [CalendarController::class, 'edit'])->name('calendar.edit'); // 予定編集フォーム
    Route::put('/calendar/{id}', [CalendarController::class, 'update'])->name('calendar.update'); // 予定更新
    Route::delete('/calendar/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy'); // 予定削除

    Route::get('/shopping', [ShoppingListController::class, 'index'])->name('shopping.index');
    Route::post('/shopping', [ShoppingListController::class, 'store'])->name('shopping.store');
    Route::post('/shopping/{id}/status', [ShoppingListController::class, 'updateStatus'])->name('shopping.updateStatus');
    Route::delete('/shopping/{id}', [ShoppingListController::class, 'destroy'])->name('shopping.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

});