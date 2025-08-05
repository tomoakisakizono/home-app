<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gratitude;
use Illuminate\Support\Facades\Auth;

class GratitudeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:255',
        ]);

        Gratitude::create([
            'user_id' => Auth::id(),
            'pair_id' => Auth::user()->pair_id,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'ありがとうを記録しました');
    }
}
