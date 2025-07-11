<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Calendar;
use App\Notifications\MessagePosted;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('pair_id', $this->pair->id)->latest()->get();

        return view('messages.index', [
            'messages' => $messages,
            'pair' => $this->pair,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_title' => 'nullable|string|max:100',
            'event_description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $calendar = null;
            if ($request->event_date) {
                $calendar = Calendar::create([
                    'pair_id' => $this->pair->id,
                    'user_id' => $this->authUser->id,
                    'event_date' => $request->event_date,
                    'event_time' => $request->event_time,
                    'title' => $request->event_title ?? 'メッセージ連携イベント',
                    'description' => $request->event_description ?? 'メッセージと連携した予定',
                ]);
            }

            $message = Message::create([
                'user_id' => $this->authUser->id,
                'pair_id' => $this->pair->id,
                'calendar_id' => $calendar?->id,
                'content' => $request->content,
            ]);

            $partner = $this->pair->user1_id === $this->authUser->id
                ? $this->pair->user2
                : $this->pair->user1;

            if ($partner) {
                $partner->notify(new MessagePosted($message));
            }

            DB::commit();
            return redirect()->route('messages.index')->with('success', 'メッセージを投稿しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'メッセージの投稿中にエラーが発生しました。')->withInput();
        }
    }

    public function edit($id)
    {
        $message = Message::where('id', $id)->where('user_id', $this->authUser->id)->firstOrFail();
        return view('messages.edit', compact('message'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:255']);

        $message = Message::where('id', $id)->where('user_id', $this->authUser->id)->firstOrFail();
        $message->update(['content' => $request->content]);

        return redirect()->route('messages.index')->with('success', 'メッセージを更新しました！');
    }

    public function destroy($id)
    {
        $message = Message::where('id', $id)->where('user_id', $this->authUser->id)->firstOrFail();
        $message->delete();

        return redirect()->route('messages.index')->with('success', 'メッセージを削除しました！');
    }
}
