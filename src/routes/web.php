<?php

use Illuminate\Support\Facades\Route;
// ===== 認証 =====
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
// ===== 画面コントローラ =====
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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PairController;

// ==================== Public（認証前） ====================

// 登録
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// ログイン・ログアウト
Route::get('/', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// 旧URLの恒久リダイレクト
Route::permanentRedirect('/home', '/dashboard');

// ==================== Private（認証後） ====================
Route::middleware('auth')->group(function () {

    // ----- ダッシュボード -----
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----- ユーザー設定（プロフィール） -----
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('/update', [UsersController::class, 'update'])->name('update');
        Route::post('/update-image', [UsersController::class, 'updateImage'])->name('updateImage');
    });

    // ----- ファミリー -----
    Route::prefix('family')->name('family.')->group(function () {
        Route::get('/', [FamilyController::class, 'show'])->name('show');

        // 招待
        Route::get('/invite', [FamilyController::class, 'inviteForm'])->name('invite.form');
        Route::post('/invite-code', [FamilyController::class, 'generateInviteCode'])->name('invite.code');
        Route::post('/invite/send', [FamilyController::class, 'sendInvite'])->name('invite.send');

        // 参加
        Route::get('/join', [FamilyController::class, 'showJoinForm'])->name('join.form');
        Route::post('/join', [FamilyController::class, 'joinFamily'])->name('join');

        // メンバー作成（管理者）
        Route::get('/member/create', [FamilyMemberController::class, 'create'])->name('member.create');
        Route::post('/member', [FamilyMemberController::class, 'store'])->name('member.store');
    });

    // ----- メッセージ -----
    Route::prefix('messages')->name('messages.')->group(function () {
        // 一覧（まずは選択画面へ）
        Route::get('/', [MessageController::class, 'index'])->name('index');

        // 家族全体チャット
        Route::get('/family', [MessageController::class, 'familyChat'])->name('family');

        // 個別チャット（Route Model Binding: {user}）
        Route::get('/user/{user}', [MessageController::class, 'userChat'])->name('user');

        // 投稿
        Route::post('/', [MessageController::class, 'store'])->name('store');
    });

    // ----- カレンダー -----
    // URLは /calendar に統一（resource 名も calendar.XXX）
    Route::resource('calendar', CalendarController::class)->names('calendar');

    // ----- 買い物リスト -----
    Route::prefix('shopping')->name('shopping.')->group(function () {
        Route::get('/', [ShoppingListController::class, 'index'])->name('index');     // GET  /shopping
        Route::post('/', [ShoppingListController::class, 'store'])->name('store');    // POST /shopping
        Route::post('/{id}/status', [ShoppingListController::class, 'updateStatus'])
            ->whereNumber('id')->name('updateStatus');
        Route::delete('/{id}', [ShoppingListController::class, 'destroy'])
            ->whereNumber('id')->name('destroy');
    });

    // ----- カテゴリ -----
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'destroy'])
        ->names('categories');

    // ----- 写真 -----
    Route::prefix('photos')->name('photos.')->group(function () {
        Route::get('/', [PhotoController::class, 'index'])->name('index');
        Route::post('/', [PhotoController::class, 'store'])->name('store');
        Route::post('/multiple-upload', [PhotoController::class, 'multipleUpload'])->name('multipleUpload');

        Route::get('/{photo}', [PhotoController::class, 'show'])->whereNumber('photo')->name('show');
        Route::get('/{photo}/edit', [PhotoController::class, 'edit'])->whereNumber('photo')->name('edit');
        Route::put('/{photo}', [PhotoController::class, 'update'])->whereNumber('photo')->name('update');

        Route::get('/download/{photoImage}', [PhotoController::class, 'download'])->whereNumber('photoImage')->name('download');
        Route::get('/download-all/{photo}', [PhotoController::class, 'downloadAll'])->whereNumber('photo')->name('downloadAll');

        Route::delete('/{photo}/images/{photoImage}', [PhotoController::class, 'deleteImage'])
            ->whereNumber('photo')->whereNumber('photoImage')->name('deleteImage');
        Route::delete('/{photo}', [PhotoController::class, 'destroy'])->whereNumber('photo')->name('destroy');
    });

    // ----- 動画 -----
    Route::resource('videos', VideoController::class)->names('videos');

    // ----- タスク -----
    Route::resource('tasks', TaskController::class)->names('tasks');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])
        ->whereNumber('task')->name('tasks.toggle');

    // ----- 通知 -----
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index'); // /notifications
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    });

    // ----- 旧ペア機能（将来削除） -----
    Route::prefix('pair')->name('pair.')->group(function () {
        Route::get('/', [PairController::class, 'show'])->name('show');
        Route::get('/setup', [PairController::class, 'setup'])->name('setup');
        Route::post('/invite', [PairController::class, 'invite'])->name('invite');
        Route::post('/accept', [PairController::class, 'accept'])->name('accept');
        Route::get('/edit', [PairController::class, 'edit'])->name('edit');
        Route::post('/update_image', [PairController::class, 'updateImage'])->name('update_image');
        Route::post('/update_name', [PairController::class, 'updateName'])->name('update_name');
        Route::post('/decline/{pair_id}', [PairController::class, 'decline'])->whereNumber('pair_id')->name('decline');
    });
});
