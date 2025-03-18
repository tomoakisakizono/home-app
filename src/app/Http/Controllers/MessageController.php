<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Pair;
use App\Models\Calendar;

class MessageController extends Controller
{
    // 🔹 メッセージ一覧
    public function index()
    {
        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->where('status', 'accepted')->first();
        
        if (!$pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアが設定されていません。');
        }

        // 🔹 すべてのメッセージ取得（最新順）
        $messages = Message::where('pair_id', $pair->id)->latest()->get();

        return view('messages.index', compact('messages', 'pair'));
    }

    // 🔹 メッセージ作成
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_title' => 'nullable|string|max:100',
            'event_description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->where('status', 'accepted')->first();

        if (!$pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }

        // 🔹 カレンダー登録（カレンダー連携がある場合）
        $calendar = null;
        if ($request->event_date) {
            $calendar = Calendar::create([
                'pair_id' => $pair->id,
                'user_id' => $user->id,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'title' => $request->event_title ?? 'メッセージ連携イベント',
                'description' => $request->event_description ?? 'メッセージと連携した予定',
            ]);
        }

        // 🔹 メッセージ登録
        Message::create([
            'user_id' => $user->id,
            'pair_id' => $pair->id,
            'calendar_id' => $calendar ? $calendar->id : null, // カレンダーと連携
            'content' => $request->content,
        ]);

        return redirect()->route('messages.index')->with('success', 'メッセージを投稿しました！');
    }

    // 🔹 メッセージ編集
    public function edit($id)
    {
        $message = Message::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('messages.edit', compact('message'));
    }

    // 🔹 メッセージ更新
    public function update(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:255']);

        $message = Message::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $message->update(['content' => $request->content]);

        return redirect()->route('messages.index')->with('success', 'メッセージを更新しました！');
    }

    // 🔹 メッセージ削除
    public function destroy($id)
    {
        $message = Message::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $message->delete();

        return redirect()->route('messages.index')->with('success', 'メッセージを削除しました！');
    }
}
