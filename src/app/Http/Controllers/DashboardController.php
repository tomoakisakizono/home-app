<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Calendar;
use App\Models\Gratitude;
use App\Models\Task;
use App\Models\Photo;
use App\Models\Video;
use App\Models\ShoppingList;
use Illuminate\Notifications\DatabaseNotification as Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $family    = $user->family;               // null の可能性あり
        $familyId  = $family->id ?? null;
        $emptyList = collect();                   // 空の反復可能

        // --- 初期化（family が無い場合の安全値） ---
        $todayEvent            = null;
        $latestMessage         = null;
        $gratitudeCountToday   = 0;
        $shoppingOpenCount     = 0;
        $photoCount            = 0;
        $videoCount            = 0;
        $taskOpenCount         = 0;
        $photos                = $emptyList;
        $messages              = $emptyList;
        $unreadMessageCount    = 0;

        // --- family があるときのみクエリを発行 ---
        if ($familyId) {
            $todayEvent = Calendar::where('family_id', $familyId)
                ->whereDate('event_date', today())
                ->orderBy('event_time')     // ✅ 追加
                ->orderBy('id')
                ->first();

            $latestMessage = Message::where('family_id', $familyId)
                ->latest()
                ->first();

            $gratitudeCountToday = Gratitude::where('family_id', $familyId)
                ->whereDate('created_at', today())
                ->count();

            $shoppingOpenCount = ShoppingList::where('family_id', $familyId)
                ->where('status', '!=', 'done')
                ->count();

            $photos = Photo::with(['images' => function ($q) {
                $q->orderBy('id');   // 先頭を決める
            }])
                ->where('family_id', $familyId)
                ->latest()
                ->limit(8)
                ->get();

            $messages = Message::where('family_id', $familyId)
                ->latest()
                ->limit(5)
                ->get();

            $photoCount = Photo::where('family_id', $familyId)->count();
            $videoCount = Video::where('family_id', $familyId)->count();
            $taskOpenCount = Task::where('family_id', $familyId)
                ->where('is_done', false)
                ->count();

            // 自分宛て未読メッセージ数（バッジなどに使用）
            $unreadMessageCount = Message::where('family_id', $familyId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        // --- 通知（DatabaseNotification） ---
        $notifications = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', \App\Models\User::class)
            ->latest()
            ->limit(10)
            ->get();

        $unreadNotificationCount = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', \App\Models\User::class)
            ->whereNull('read_at')
            ->count();

        // --- サイドメニュー等のバッジ ---
        $menuItems = [
            [
                'label' => 'メッセージ',
                'icon'  => 'bi-chat-dots',
                'route' => 'messages.family',
                'badge' => $unreadMessageCount,
            ],
            [
                'label' => '予定',
                'icon'  => 'bi-calendar-event',
                'route' => 'calendar.index',
                'badge' => $familyId
                    ? Calendar::where('family_id', $familyId)->whereDate('event_date', today())->count()
                    : 0,
            ],
            [
                'label' => '買い物',
                'icon'  => 'bi-cart',
                'route' => 'shopping.index',
                'badge' => $shoppingOpenCount,
            ],
            [
                'label' => '写真',
                'icon'  => 'bi-image',
                'route' => 'photos.index',
                'badge' => $photoCount,
            ],
            [
                'label' => '動画',
                'icon'  => 'bi-camera-video',
                'route' => 'videos.index',
                'badge' => $videoCount,
            ],
            [
                'label' => 'タスク',
                'icon'  => 'bi-check-square',
                'route' => 'tasks.index',
                'badge' => $taskOpenCount,
            ],
        ];

        return view('dashboard', [
            'family'                   => $family,
            'familyMembers'            => $family?->users ?? $emptyList, // foreach 安全
            'todayEvent'               => $todayEvent,
            'latestMessage'            => $latestMessage,
            'gratitudeCount'           => $gratitudeCountToday,
            'shoppingOpen'             => $shoppingOpenCount,
            'photos'                   => $photos,
            'messages'                 => $messages,
            'photoCount'               => $photoCount,
            'videoCount'               => $videoCount,
            'taskOpen'                 => $taskOpenCount,
            'notifications'            => $notifications,
            'unreadMessageCount'       => $unreadMessageCount,
            'unreadNotificationCount'  => $unreadNotificationCount,
            'menuItems'                => $menuItems,
        ]);
    }
}
