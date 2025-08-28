<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CalendarRequest;

class CalendarController extends Controller
{
    public function index()
    {
        $familyId = auth()->user()->family_id;

        $events = Calendar::where('family_id', $familyId) // ✅ family 基準に変更
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->get();

        return view('calendar.index', compact('events'));
    }

    public function store(CalendarRequest $request)
    {
        DB::beginTransaction();
        try {
            $auth = auth()->user();

            $calendar = Calendar::create([
                'family_id'   => $auth->family_id,          // ✅ これが重要
                'pair_id'     => $this->pair->id ?? null,   // 互換のため残してOK
                'user_id'     => $auth->id,
                'title'       => $request->title,
                'event_date'  => $request->event_date,
                'event_time'  => $request->event_time,
                'description' => $request->description,
            ]);

            // 通知（既存ロジックのまま）
            $partner = User::where('family_id', $auth->family_id)
                        ->where('id', '!=', $auth->id)
                        ->first();

            if ($partner) {
                $calendar->user = $auth;
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

    public function update(CalendarRequest $request, $id)
    {
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
