<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\TaskController;

// ========== 認証関連 ==========

// 登録
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// ログイン・ログアウト
Route::get('/', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// /home リダイレクト
Route::get('/home', fn () => redirect()->route('dashboard'));

// ========== 認証後の機能 ==========

Route::middleware(['auth'])->group(function () {

    // ===== ダッシュボード =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== ユーザー関連 =====
    Route::get('/users/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/update', [UsersController::class, 'update'])->name('users.update');
    Route::post('/users/update-image', [UsersController::class, 'updateImage'])->name('users.updateImage');

    // ===== ファミリー関連 =====
    Route::get('/family', [FamilyController::class, 'show'])->name('family.show');
    Route::post('/family/invite-code', [FamilyController::class, 'generateInviteCode'])->name('family.invite');
    Route::get('/family/invite', [FamilyController::class, 'inviteForm'])->name('family.invite.form');
    Route::post('/family/invite/send', [FamilyController::class, 'sendInvite'])->name('family.invite.send');
    Route::get('/family/join', [FamilyController::class, 'showJoinForm'])->name('family.join');
    Route::post('/family/join', [FamilyController::class, 'joinFamily'])->name('family.join.post');

    // 管理者によるメンバー作成
    Route::get('/family/member/create', [FamilyMemberController::class, 'create'])->name('family.member.create');
    Route::post('/family/member', [FamilyMemberController::class, 'store'])->name('family.member.store');

    // ===== メッセージ関連（family構成） =====
    Route::get('/messages/family', [MessageController::class, 'familyChat'])->name('messages.family');
    Route::get('/messages/{user}', [MessageController::class, 'userChat'])->name('messages.user');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // ===== カレンダー =====
    Route::resource('calendar', CalendarController::class);

    // ===== 買い物リスト =====
    Route::prefix('shopping')->name('shopping.')->group(function () {
        Route::get('/', [ShoppingListController::class, 'index'])->name('index');
        Route::post('/', [ShoppingListController::class, 'store'])->name('store');
        Route::post('/{id}/status', [ShoppingListController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [ShoppingListController::class, 'destroy'])->name('destroy');
    });

    // ===== カテゴリ =====
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

    // ===== 写真共有 =====
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

    // ===== 動画共有 =====
    Route::resource('videos', VideoController::class);

    // ===== タスクリスト =====
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    // ====== 通知関連 ======
    Route::get('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'ok']);
    })->name('notifications.read');

    // ===== ペア機能（旧構成：削除予定） =====
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
});
