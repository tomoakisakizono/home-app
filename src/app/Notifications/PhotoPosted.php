<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Photo;

class PhotoPosted extends Notification
{
    use Queueable;

    protected $photo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Get the Database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->photo->user->name . 'さんが新しい写真を投稿しました。',
            'link' => route('photos.show', $this->photo->id),
        ];
    }
}
