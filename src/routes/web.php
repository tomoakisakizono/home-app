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
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\TaskController;

// ユーザ登録
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// ログイン
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// 認証後のルーティング
Route::middleware(['auth'])->group(function () {
    // ペア関連
    Route::prefix('pair')->name('pair.')->group(function () {
        Route::get('/', [PairController::class, 'show'])->name('show');
        Route::get('/setup', [PairController::class, 'setup'])->name('setup');
        Route::post('/invite', [PairController::class, 'invite'])->name('invite');
        Route::post('/accept', [PairController::class, 'accept'])->name('accept');
        Route::get('/edit', [PairController::class, 'edit'])->name('edit');
        Route::post('/update_image', [PairController::class, 'updateImage'])->name('update_image');
        Route::post('/update_name', [PairController::class, 'updateName'])->name('update_name');
        Route::post('/decline/{pair_id}', [PairController::class, 'decline'])->name('decline');
    });

    // 機能記録
    Route::get('/functions', [FunctionController::class, 'index'])->name('functions.index');
    Route::post('/functions', [FunctionController::class, 'store'])->name('functions.store');

    // メッセージ
    Route::resource('messages', MessageController::class)->except(['create', 'show']);

    // カレンダー
    Route::resource('calendar', CalendarController::class);

    // 買い物リスト
    Route::prefix('shopping')->name('shopping.')->group(function () {
        Route::get('/', [ShoppingListController::class, 'index'])->name('index');
        Route::post('/', [ShoppingListController::class, 'store'])->name('store');
        Route::post('/{id}/status', [ShoppingListController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [ShoppingListController::class, 'destroy'])->name('destroy');
    });

    // カテゴリ
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

    // 写真
    Route::prefix('photos')->name('photos.')->group(function () {
        Route::get('/', [PhotoController::class, 'index'])->name('index');
        Route::post('/', [PhotoController::class, 'store'])->name('store');
        Route::post('/multiple-upload', [PhotoController::class, 'multipleUpload'])->name('multipleUpload');
        Route::get('/{photo}', [PhotoController::class, 'show'])->name('show');
        Route::get('/{photo}/edit', [PhotoController::class, 'edit'])->name('edit');
        Route::put('/{photo}', [PhotoController::class, 'update'])->name('update');
        Route::get('/download/{photoImage}', [PhotoController::class, 'download'])->name('download');
        Route::get('/download-all/{photo}', [PhotoController::class, 'downloadAll'])->name('downloadAll');
        Route::delete('/{photo}/images/{photoImage}', [PhotoController::class, 'deleteImage'])->name('deleteImage');
        Route::delete('/{photo}', [PhotoController::class, 'destroy'])->name('destroy');
    });

    // 動画
    Route::resource('videos', VideoController::class);

    // 作業リスト
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    Route::get('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'ok']);
    })->name('notifications.read');
});
