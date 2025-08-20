<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Carbon のロケールをアプリに合わせる
        Carbon::setLocale(app()->getLocale()); // :contentReference[oaicite:2]{index=2}

        // サーバのロケール（曜日名などの i18n に寄与）
        setlocale(LC_TIME, 'ja_JP.UTF-8');
        date_default_timezone_set(config('app.timezone'));
    }
}
