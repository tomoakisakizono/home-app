<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Calendar::where('pair_id', $this->pair->id)
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

        DB::beginTransaction();
        try {
            $calendar = Calendar::create([
                'pair_id' => $this->pair->id,
                'user_id' => $this->authUser->id,
                'title' => $request->title,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'description' => $request->description,
            ]);

            $partner = User::where('pair_id', $this->pair->id)
                        ->where('id', '!=', $this->authUser->id)
                        ->first();

            if ($partner) {
                $calendar->user = $this->authUser;
                $partner->notify(new CalendarEventCreated($calendar));
            }

            DB::commit();
            return redirect()->route('calendar.index')->with('success', '予定を追加しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '予定の登録中にエラーが発生しました。')->withInput();
        }
    }

    public function edit($id)
    {
        $event = Calendar::findOrFail($id);

        if ($event->pair_id !== $this->pair?->id) {
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

        if ($event->pair_id !== $this->pair?->id) {
            abort(403, '許可されていません');
        }

        DB::beginTransaction();
        try {
            $event->update($request->all());

            DB::commit();
            return redirect()->route('calendar.index')->with('success', '予定を更新しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '予定の更新に失敗しました。')->withInput();
        }
    }

    public function destroy($id)
    {
        $event = Calendar::findOrFail($id);
        $event->delete();

        return redirect()->route('calendar.index')->with('success', '予定を削除しました！');
    }
}
