<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pair;
use App\Models\User;
use App\Models\FunctionRecord;
use Illuminate\Support\Facades\Storage;

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
    
        // **自分が user1_id でないことを確認**
        if ($pair->user1_id === $user->id) {
            return redirect()->back()->with('error', '自分の招待コードは使用できません。');
        }
        // 🔹 ペアネームを作成
        $user1 = User::find($pair->user1_id);
        $pairName = $user1->name . ' & ' . $user->name;

        // 🔹 デフォルト画像のパスを設定
        $defaultImagePath = 'images/default_pair.png';

        // **ペアを確定**
        $pair->update([
            'user2_id' => $user->id,
            'pair_name' => $pairName, // 🔹 ペアネームを保存
            'pair_image' => $defaultImagePath, // 🔹 ここにデフォルト画像を設定！
            'status' => 'accepted'
        ]);

        // ✅ `users` テーブルの `pair_id` を更新（両者に設定）
        User::where('id', $pair->user1_id)->update(['pair_id' => $pair->id]); // 招待したユーザー
        User::where('id', $pair->user2_id)->update(['pair_id' => $pair->id]); // 承認したユーザー

        return redirect()->route('pair.show')->with('success', 'ペアが成立しました！');
    }

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

        // 🔹 直近の登録内容を取得（最新3件）
        $latestFunctions = FunctionRecord::where('user_id', $user->id)
                            ->latest()
                            ->take(3)
                            ->get();
    
        return view('pair.show', compact('user', 'partner', 'pair', 'latestFunctions'));
    }

    public function edit()
    {
        $user = Auth::user();
        $pair = Pair::where(function ($query) use ($user) {
                    $query->where('user1_id', $user->id)
                        ->orWhere('user2_id', $user->id);
                })
                ->where('status', 'accepted')
                ->first();

        if (!$pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアが設定されていません。');
        }

        // 🔹 ペアの相手の情報を取得
        $partner = ($pair->user1_id === $user->id) ? User::find($pair->user2_id) : User::find($pair->user1_id);

        // 🔹 `$partner` が `NULL` ならエラー回避
        if (!$partner) {
            return redirect()->route('pair.setup')->with('error', 'ペアの相手が見つかりませんでした。');
        }
        
        return view('pair.edit', compact('user', 'partner', 'pair'));
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'pair_image' => 'image|mimes:jpg,jpeg,png,webp,heic|max:4096',
        ]);
    
        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->where('status', 'accepted')
                    ->first();
    
        if (!$pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }
    
        // 🔽 画像アップロード処理
        if ($request->hasFile('pair_image')) {
            // 新しい画像を保存
            $path = $request->file('pair_image')->store('pair_images', 'public');
    
            // 古い画像がある場合は削除
            if ($pair->pair_image) {
                Storage::disk('public')->delete($pair->pair_image);
            }
    
            // DB更新
            $pair->update(['pair_image' => $path]);
        }
    
        return redirect()->route('pair.edit')->with('success', 'ペア画像を更新しました！');
    }
    
    public function updateName(Request $request)
    {
        $request->validate([
            'pair_name' => 'required|string|max:50', // 50文字以内でペアネームを制限
        ]);

        $user = Auth::user();
        $pair = Pair::where(function ($query) use ($user) {
                        $query->where('user1_id', $user->id)
                            ->orWhere('user2_id', $user->id);
                    })
                    ->where('status', 'accepted')
                    ->first();

        if (!$pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }

        // 🔹 ペアネームを更新
        $pair->update(['pair_name' => $request->pair_name]);

        return redirect()->route('pair.edit')->with('success', 'ペアネームを更新しました！');
    }

}
