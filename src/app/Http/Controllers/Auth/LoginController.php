<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // ログイン成功後に Auth::id() が null でないか確認
            $userId = Auth::id();
            if (!$userId) {
                return back()->withErrors(['email' => 'ログイン後にエラーが発生しました。']);
            }
            return redirect()->route('users.show', ['id' => $userId])->with('success', 'ログインしました！');
        }
        return back()->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
    }
}
