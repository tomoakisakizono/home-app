<?php

namespace App\Observers;

use App\Models\Message;
use App\Notifications\GenericItemCreated;

class MessageObserver
{
    public function created(Message $message): void
    {
        // 受信者へ通知（個人チャット）: receiver_id があれば通知
        if ($message->receiver_id) {
            $receiver = $message->receiver;
            if ($receiver) {
                $receiver->notify(new GenericItemCreated(
                    title: '新しいメッセージ',
                    message: mb_strimwidth($message->content, 0, 60, '…'),
                    url: route('messages.index') // 必要に応じて詳細URLへ
                ));
            }
        }

        // ファミリーチャット: family_id がある場合は（送信者以外）へ一斉通知
        if ($message->family_id && !$message->receiver_id) {
            $family = $message->family;
            if ($family && $family->users) {
                foreach ($family->users as $user) {
                    if ($user->id !== $message->sender_id) {
                        $user->notify(new GenericItemCreated(
                            title: 'ファミリーメッセージ',
                            message: mb_strimwidth($message->content, 0, 60, '…'),
                            url: route('messages.index')
                        ));
                    }
                }
            }
        }
    }
}
