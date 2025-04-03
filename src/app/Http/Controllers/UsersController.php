<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UsersController extends Controller
{
    // 🔹 ユーザー情報編集画面表示（画像アップロード付き）
    public function edit()
    {
        $user = Auth::user(); // 自分自身の情報を取得

        if (!$user) {
            abort(404, 'ユーザーが見つかりません');
        }

        // ペア情報の取得
        $pair = \App\Models\Pair::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id);
        })->where('status', 'accepted')->first();

        // パートナー情報の取得
        $partner = null;
        if ($pair) {
            $partnerId = $pair->user1_id === $user->id ? $pair->user2_id : $pair->user1_id;
            $partner = User::find($partnerId);
        }

        return view('users.edit', compact('user', 'pair', 'partner'));
    }

    // プロフィール更新（名前・メール・パスワード）
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return back()->with('success', 'プロフィールを更新しました！');
    }

    // 🔹 プロフィール画像の更新処理
    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');

            // 古い画像がある場合は削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像を保存
            $user->update(['profile_image' => $path]);
        }

        return back()->with('success', 'プロフィール画像を更新しました！');
    }
}
