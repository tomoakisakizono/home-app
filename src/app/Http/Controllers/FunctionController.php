<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FunctionRecord;
use App\Models\User;

class FunctionController extends Controller
{
    public function index()
    {
        // ユーザーごとの履歴取得
        $latestFunctions = FunctionRecord::where('user_id', $this->authUser->id)
            ->latest()
            ->take(3)
            ->get();

        return view('pair.show', [
            'pair' => $this->pair,
            'latestFunctions' => $latestFunctions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'function_name' => 'required|string|max:50',
            'details' => 'required|string|max:255',
        ]);

        FunctionRecord::create([
            'pair_id' => $this->pair->id,
            'function_name' => $request->function_name,
            'details' => $request->details,
            'user_id' => $this->authUser->id,
        ]);

        return redirect()->back()->with('success', '機能を登録しました！');
    }
}
