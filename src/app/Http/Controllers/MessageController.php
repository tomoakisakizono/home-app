<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\MessagePosted;

class MessageController extends Controller
{
    // 全体または個別のメッセージを表示（旧構成は使わない）
    public function index()
    {
        return redirect()->route('messages.select');
    }

    // 個別チャット（1対1）
    public function userChat(User $user)
    {
        $authId = auth()->id();
        $familyId = auth()->user()->family_id;

        // 双方向のやり取りだけを取得
        $messages = Message::where('family_id', $familyId)
            ->where(function ($q) use ($authId, $user) {
                $q->where(function ($q2) use ($authId, $user) {
                    $q2->where('sender_id', $authId)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($q2) use ($authId, $user) {
                    $q2->where('sender_id', $user->id)
                        ->where('receiver_id', $authId);
                });
            })
        ->orderBy('created_at')
        ->get();

        return view('messages.index', [
            'messages' => $messages,
            'chatPartner' => $user,
        ]);
    }

    // 家族全体チャット
    public function familyChat()
    {
        $familyId = auth()->user()->family_id;

        $messages = Message::where('family_id', $familyId)
            ->whereNull('receiver_id')
            ->orderBy('created_at')
            ->get();

        return view('messages.index', [
            'messages' => $messages,
            'chatPartner' => null,
        ]);
    }

    // メッセージ送信（全体 or 個別）
    public function store(MessageRequest $request)
    {
        DB::beginTransaction();

        try {
            $authUser = auth()->user();

            $message = Message::create([
                'sender_id'   => $authUser->id,
                'receiver_id' => $request->receiver_id ?? null,
                'family_id'   => $authUser->family_id,
                'content'     => $request->content,
                'is_read'     => false,
            ]);

            // 通知処理
            if ($message->receiver_id) {
                $receiver = User::find($message->receiver_id);
                if ($receiver) {
                    $receiver->notify(new MessagePosted($message));
                }
            }

            DB::commit();

            return $message->receiver_id
                ? redirect()->route('messages.user', $message->receiver_id)->with('success', '送信しました！')
                : redirect()->route('messages.family')->with('success', '家族全体に送信しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'メッセージ送信中にエラーが発生しました。')->withInput();
        }
    }

    // メッセージ編集（自分のメッセージのみ）
    public function edit($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        return view('messages.edit', compact('message'));
    }

    // メッセージ更新
    public function update(MessageRequest $request, $id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->update(['content' => $request->content]);

        return redirect()->route('messages.select')->with('success', 'メッセージを更新しました！');
    }

    // メッセージ削除
    public function destroy($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->delete();

        return redirect()->route('messages.select')->with('success', 'メッセージを削除しました！');
    }
}
