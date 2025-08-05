<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InviteFamilyMemberNotification;

class FamilyController extends Controller
{
    // ファミリー詳細表示
    public function show()
    {
        $family = Auth::user()->family;

        if (!$family) {
            return redirect()->route('dashboard')->with('error', 'ファミリーに所属していません。');
        }

        return view('family.show', compact('family'));
    }

    // 招待コード生成
    public function generateInviteCode()
    {
        $user = Auth::user();
        $family = $user->family;

        if (!$family) {
            return redirect()->route('dashboard')->with('error', 'ファミリーに所属していません。');
        }

        // 既にある場合は使い回す
        if (!$family->invite_code) {
            $family->invite_code = Str::random(8);
            $family->save();
        }

        return redirect()->route('family.show')->with('success', '招待コードを発行しました。');
    }

    // 招待フォーム表示
    public function inviteForm()
    {
        $family = Auth::user()->family;

        if (!$family || !$family->invite_code) {
            return redirect()->route('family.show')->with('error', '招待コードが未発行です。');
        }

        return view('family.invite', ['invite_code' => $family->invite_code]);
    }

    // メールで招待送信
    public function sendInvite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $family = Auth::user()->family;

        Notification::route('mail', $request->email)
            ->notify(new InviteFamilyMemberNotification(
                $family->invite_code,
                $family->name ?? 'あなたのファミリー'
            ));

        return back()->with('success', '招待メールを送信しました！');
    }

    public function showJoinForm()
    {
        return view('family.join');
    }

    // 招待コードでファミリーに参加
    public function joinFamily(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:families,invite_code',
        ]);

        $user = Auth::user();

        if ($user->family_id) {
            return redirect()->route('dashboard')->with('error', 'すでにファミリーに所属しています。');
        }

        $family = Family::where('invite_code', $request->invite_code)->first();

        if (!$family) {
            return redirect()->back()->with('error', '招待コードが無効です。');
        }

        $user->family_id = $family->id;
        $user->save();

        return redirect()->route('family.show')->with('success', 'ファミリーに参加しました！');
    }
}
