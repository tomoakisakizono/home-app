<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericItemCreated extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,      // 通知タイトル
        public string $message,    // 本文
        public ?string $url = null // 詳細へ誘導するURL
    ) {
    }

    public function via(object $notifiable): array
    {
        // まずは DB のみ（後で broadcast/mail を追加可能）:contentReference[oaicite:5]{index=5}
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }
}
