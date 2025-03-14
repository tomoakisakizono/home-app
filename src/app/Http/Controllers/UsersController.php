<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index($id) {

        $user = User::find($id);

        if (!$user) {
            abort(404,'ユーザーが見つかりません');
        }
        return view('users.show', compact('user'));
    }
}