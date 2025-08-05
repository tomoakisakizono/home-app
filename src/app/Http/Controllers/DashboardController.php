<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Calendar;
use App\Models\Gratitude;
use App\Models\Task;
use App\Models\Photo;
use App\Models\Video;
use App\Models\ShoppingList;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $familyId = $user->family_id;

        return view('dashboard', [
            'familyMembers' => $user->family->users ?? [],
            'unreadCount' => Message::where('family_id', $familyId)
                                    ->where('receiver_id', $user->id)
                                    ->where('is_read', false)
                                    ->count(),
            'todayEvent' => Calendar::where('family_id', $familyId)
                                    ->whereDate('event_date', today())
                                    ->first(),
            'latestMessage' => Message::where('family_id', $familyId)
                                    ->latest()
                                    ->first(),
            'gratitudeCount' => Gratitude::where('family_id', $familyId)
                                        ->whereDate('created_at', today())
                                        ->count(),
            'menuItems' => [
                [
                    'label' => 'メッセージ',
                    'icon' => 'bi-chat-dots',
                    'route' => 'messages.family',
                    'badge' => Message::where('family_id', $familyId)
                                    ->where('receiver_id', $user->id)
                                    ->where('is_read', false)
                                    ->count()
                ],
                [
                    'label' => '予定',
                    'icon' => 'bi-calendar-event',
                    'route' => 'calendar.index',
                    'badge' => Calendar::where('family_id', $familyId)
                                    ->whereDate('event_date', today())
                                    ->count()
                ],
                [
                    'label' => '買い物',
                    'icon' => 'bi-cart',
                    'route' => 'shopping.index',
                    'badge' => ShoppingList::where('family_id', $familyId)
                                        ->where('status', '!=', 'done')
                                        ->count()
                ],
                [
                    'label' => '写真',
                    'icon' => 'bi-image',
                    'route' => 'photos.index',
                    'badge' => Photo::where('family_id', $familyId)->count()
                ],
                [
                    'label' => '動画',
                    'icon' => 'bi-camera-video',
                    'route' => 'videos.index',
                    'badge' => Video::where('family_id', $familyId)->count()
                ],
                [
                    'label' => 'タスク',
                    'icon' => 'bi-check-square',
                    'route' => 'tasks.index',
                    'badge' => Task::where('family_id', $familyId)
                                ->where('is_done', false)
                                ->count()
                ],
            ]
        ]);
    }
}
