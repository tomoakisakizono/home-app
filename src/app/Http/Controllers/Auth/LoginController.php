<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // ログイン成功後に Auth::id() が null でないか確認
            $userId = Auth::id();
            if (!$userId) {
                return back()->withErrors(['email' => 'ログイン後にエラーが発生しました。']);
            }
            return redirect()->route('pair.show', ['id' => $userId])->with('success', 'ログインしました！');
        }
        return back()->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
    }
}
