<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
// ★ Observer で使用
use App\Models\Message;
use App\Observers\MessageObserver;

/**
 * アプリケーションのイベント/リスナー/オブザーバ登録を担うプロバイダ
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションのイベントリスナー マッピング
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // ユーザー登録時のメール認証通知（必要なければ削除可）
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * 全アプリケーションイベントの登録
     */
    public function boot(): void
    {
        // ---- Model Observers ----
        // メッセージ作成時に通知を発火（MessageObserver@created）
        Message::observe(MessageObserver::class);
    }

    /**
     * 自動ディスカバリを使うかどうか
     * ここを true にすると app/Listeners 配下などを自動探索します
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
