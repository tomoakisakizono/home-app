<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pair;
use App\Models\FunctionRecord;

class FunctionController extends Controller
{
    public function index()
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

        //ユーザーごとの履歴取得
        $latestFunctions = FunctionRecord::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();
    dd($latestFunctions);
        return view('pair.show', compact('pair', 'latestFunctions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'function_name' => 'required|string|max:50',
            'details' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->where('status', 'accepted')
                    ->first();

        if (!$pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }

        FunctionRecord::create([
            'pair_id' => $pair->id,
            'function_name' => $request->function_name,
            'details' => $request->details,
            'user_id' => $user->id
        ]);

        return redirect()->back()->with('success', '機能を登録しました！');
    }
}
