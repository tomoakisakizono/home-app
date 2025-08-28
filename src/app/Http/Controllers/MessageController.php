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
    // ---- 共通でビューに渡す最低限のデータを作るヘルパ ----
    private function buildCommonPayload()
    {
        $auth   = auth()->user();
        $family = $auth?->family;
        $familyId = $family->id ?? null;

        // メンバー（自分以外）
        $members = $familyId
            ? $family->users()->where('users.id', '!=', $auth->id)->get()
            : collect();

        // 自分宛の未読
        $unreadCount = $familyId
            ? Message::where('family_id', $familyId)
                ->where('receiver_id', $auth->id)
                ->where('is_read', false)
                ->count()
            : 0;

        return [$auth, $family, $familyId, $members, $unreadCount];
    }

    // 一覧（Family Chat の着地点）
    public function index()
    {
        [$auth, $family, $familyId, $members, $unreadCount] = $this->buildCommonPayload();

        $messages = $familyId
            ? Message::where('family_id', $familyId)
                ->orderBy('created_at')   // 家族全体＋個別を混在表示するなら orderBy のみに
                ->limit(100)->get()
            : collect();

        return view('messages.index', [
            'family'      => $family,
            'members'     => $members,
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
            'chatPartner' => null,   // 一覧/家族チャットの時は null
        ]);
    }

    // 家族全体チャット
    public function familyChat()
    {
        [$auth, $family, $familyId, $members, $unreadCount] = $this->buildCommonPayload();

        $messages = $familyId
            ? Message::where('family_id', $familyId)
                ->whereNull('receiver_id')
                ->orderBy('created_at')
                ->limit(100)->get()
            : collect();

        return view('messages.index', [
            'family'      => $family,
            'members'     => $members,
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
            'chatPartner' => null,
        ]);
    }

    // 個別チャット
    public function userChat(User $user)
    {
        [$auth, $family, $familyId, $members, $unreadCount] = $this->buildCommonPayload();

        $messages = $familyId
            ? Message::where('family_id', $familyId)
                ->where(function ($q) use ($auth, $user) {
                    $q->where(function ($q2) use ($auth, $user) {
                        $q2->where('sender_id', $auth->id)
                           ->where('receiver_id', $user->id);
                    })->orWhere(function ($q2) use ($auth, $user) {
                        $q2->where('sender_id', $user->id)
                           ->where('receiver_id', $auth->id);
                    });
                })
                ->orderBy('created_at')
                ->limit(100)->get()
            : collect();

        return view('messages.index', [
            'family'      => $family,
            'members'     => $members,
            'messages'    => $messages,
            'unreadCount' => $unreadCount,
            'chatPartner' => $user,
        ]);
    }

    // 送信
    public function store(MessageRequest $request)
    {
        DB::beginTransaction();
        try {
            $auth = auth()->user();

            $message = Message::create([
                'sender_id'   => $auth->id,
                'receiver_id' => $request->receiver_id ?? null,
                'family_id'   => $auth->family_id,
                'content'     => $request->content,
                'is_read'     => false,
            ]);

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

    // 編集画面
    public function edit($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        return view('messages.edit', compact('message'));
    }

    // 更新（❗️行き先を messages.index に変更）
    public function update(MessageRequest $request, $id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->update(['content' => $request->content]);

        return redirect()->route('messages.index')->with('success', 'メッセージを更新しました！');
    }

    // 削除（❗️行き先を messages.index に変更）
    public function destroy($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'メッセージを削除しました！');
    }
}
