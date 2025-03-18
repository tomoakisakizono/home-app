<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\Pair; // 🔹 追加（ペア情報取得に必要）
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // 🔹 追加（日付操作に必要）

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pairId = $user->pair_id;

        if (!$pairId) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }

        $events = Calendar::where('pair_id', $pairId)->orderBy('event_date', 'asc')->get();
        
        return view('calendar.index', compact('events'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->pair_id) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'description' => 'nullable|string',
        ]);

        Calendar::create([
            'pair_id' => $user->pair_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'description' => $request->description,
        ]);

        return redirect()->route('calendar.index')->with('success', '予定を追加しました！');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
        ]);

        $event = Calendar::findOrFail($id);
        $event->update($request->all());

        return redirect()->route('calendar.index')->with('success', '予定を更新しました！');
    }

    public function destroy($id)
    {
        $event = Calendar::findOrFail($id);
        $event->delete();

        return redirect()->route('calendar.index')->with('success', '予定を削除しました！');
    }
}

