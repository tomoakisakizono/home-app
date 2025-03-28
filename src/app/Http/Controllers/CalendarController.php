<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\Pair;
use App\Notifications\CalendarEventCreated;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pairId = $user->pair_id;

        if (!$pairId) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }

        $events = Calendar::where('pair_id', $pairId)
            ->orderBy('event_date', 'asc')
            ->get();

        return view('calendar.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $pairId = $user->pair_id;

        if (!$user->pair_id) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }

        $calendar = Calendar::create([
            'pair_id' => $pairId,
            'user_id' => $user->id,
            'title' => $request->title,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'description' => $request->description,
        ]);

        $partner = \App\Models\User::where('pair_id', $pairId)
                ->where('id', '!=', $user->id)
                ->first();

        if ($partner) {
            $calendar->user = $user; // 通知クラスに渡すためにuserプロパティ追加
            $partner->notify(new CalendarEventCreated($calendar));
        }

        return redirect()->route('calendar.index')->with('success', '予定を追加しました！');
    }

    public function edit($id)
    {
        $event = Calendar::findOrFail($id);

        // 認可：ログインユーザーのペアIDが一致するかチェック
        if ($event->pair_id !== Auth::user()->pair_id) {
            abort(403, '許可されていません');
        }

        return view('calendar.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string|max:1000',
        ]);

        $event = Calendar::findOrFail($id);

        if ($event->pair_id !== Auth::user()->pair_id) {
            abort(403, '許可されていません');
        }

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

