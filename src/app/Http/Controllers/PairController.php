<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pair;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PairController extends Controller
{
    public function setup()
    {
        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->first();

        return view('pair.setup', compact('user', 'pair'));
    }
    
    // ペア招待
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $partner = User::where('email', $request->email)->first();

        if (!$partner || $partner->id === Auth::id()) {
            return redirect()->back()->with('error', '無効なユーザーです');
        }

        // **既存の招待があるか確認**
        $existingPair = Pair::where('user1_id', Auth::id())->where('status', 'pending')->first();

        if ($existingPair) {
            return redirect()->back()->with('success', "すでに発行済みの招待コード: {$existingPair->invite_code}");
        }

        // **新しい招待コードを発行**
        $inviteCode = strtoupper(bin2hex(random_bytes(4)));

        $pair = Pair::create([
            'user1_id' => Auth::id(),
            'invite_code' => $inviteCode,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', "招待コード: $inviteCode");
        dd($inviteCode);
    }  

    // ペア承認
    public function accept(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:pairs,invite_code',
        ]);
    
        $user = Auth::user();
        
        // **招待コードに対応するペアを取得（user2_id が NULL のペアのみ）**
        $pair = Pair::where('invite_code', $request->invite_code)
                    ->whereNull('user2_id')
                    ->first();
    
        if (!$pair) {
            return redirect()->back()->with('error', '無効な招待コードです、または既に使用されています。');
        }

         // **デバッグログを追加（データの確認）**
        \Log::info('ペア承認処理:', [
            'invite_code' => $request->invite_code,
            'user_id' => $user->id,
            'pair_before' => $pair
        ]);
    
        // **自分が user1_id でないことを確認**
        if ($pair->user1_id === $user->id) {
            return redirect()->back()->with('error', '自分の招待コードは使用できません。');
        }
    
        // **ペアを確定**
        $pair->update([
            'user2_id' => $user->id,
            'status' => 'accepted'
        ]);

         // **デバッグログを追加（データの確認）**
        \Log::info('ペア承認処理:', [
            'invite_code' => $request->invite_code,
            'user_id' => $user->id,
            'pair_before' => $pair
        ]);
    
        return redirect()->route('pair.show')->with('success', 'ペアが成立しました！');
    }
    
    // // ペア拒否
    // public function decline($pair_id)
    // {
    //     $pair = Pair::findOrFail($pair_id);

    //     if ($pair->user2_id !== Auth::id()) {
    //         return response()->json(['message' => '拒否できません'], 403);
    //     }

    //     $pair->delete();

    //     return response()->json(['message' => 'ペア招待を拒否しました']);
    // }

    // 自分のペア情報を取得
    public function show()
    {
        $user = Auth::user();
    
        // 🔹 自分が `user1_id` または `user2_id` のペアを取得
        $pair = Pair::where(function ($query) use ($user) {
                    $query->where('user1_id', $user->id)
                        ->orWhere('user2_id', $user->id);
                })
                ->where('status', 'accepted')
                ->first();
    
        // 🔹 ペアが見つからない場合
        if (!$pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアが設定されていません');
        }
    
        // 🔹 ペアの相手の情報を取得
        $partner = ($pair->user1_id === $user->id) ? User::find($pair->user2_id) : User::find($pair->user1_id);
    
        // 🔹 `$partner` が `NULL` ならエラー回避
        if (!$partner) {
            return redirect()->route('pair.setup')->with('error', 'ペアの相手が見つかりませんでした。');
        }
    
        return view('pair.show', compact('user', 'partner', 'pair'));
    }
}
