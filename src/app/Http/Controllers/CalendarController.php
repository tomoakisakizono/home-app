<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarRequest;
use App\Models\Calendar;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    /**
     * カレンダートップ
     */
    public function index(Request $request)
    {
        $user      = $request->user();
        $familyId  = $user->family_id;

        // 選択日（/calendar?date=YYYY-MM-DD）
        $selected = $request->query('date');

        // 表示月（/calendar?month=YYYY-MM-01 など）未指定は当月
        $monthStr     = $request->query('month');
        $currentMonth = $monthStr ? Carbon::parse($monthStr) : Carbon::today();

        $monthStart = $currentMonth->copy()->startOfMonth()->toDateString();
        $monthEnd   = $currentMonth->copy()->endOfMonth()->toDateString();

        // 今月の予定（リスト描画用）
        $events = Calendar::where('family_id', $familyId)
            ->whereBetween('event_date', [$monthStart, $monthEnd])
            ->orderBy('event_date')->orderBy('event_time')
            ->get();

        $eventsByDate = $events->groupBy('event_date');

        // 選択日の予定
        $dayEvents = collect();
        if ($selected) {
            $dayEvents = Calendar::where('family_id', $familyId)
                ->whereDate('event_date', $selected)
                ->orderBy('event_time')
                ->get();
        }

        // FullCalendar 用（必ず YYYY-MM-DD に整形してから THH:MM を付ける）
        $fcEvents = Calendar::where('family_id', $familyId)
            ->orderBy('event_date')->orderBy('event_time')
            ->get()
            ->map(function ($e) {
                // event_date は casts('date') なので Carbon → YYYY-MM-DD 化
                $date = $e->event_date instanceof Carbon
                    ? $e->event_date->toDateString()
                    : (string) $e->event_date;

                $time = $e->event_time_hi; // "HH:MM" または null（Modelアクセサ）

                return [
                    'id'          => $e->id,
                    'title'       => $e->title,
                    'start'       => $time ? "{$date}T{$time}" : $date,
                    'allDay'      => $time ? false : true,
                    'description' => $e->description,
                ];
            })->values();

        return view('calendar.index', compact('eventsByDate', 'dayEvents', 'selected', 'fcEvents'));
    }

    /**
     * 作成（秒なしで保存）
     */
    public function store(CalendarRequest $request)
    {
        $auth = $request->user();

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['family_id']  = $auth->family_id;
            $data['user_id']    = $auth->id;
            $data['event_time'] = $data['event_time'] ?: null; // "HH:MM" or null

            $calendar = Calendar::create($data);

            // 家族内の他メンバーへ通知（任意）
            $partner = User::where('family_id', $auth->family_id)
                ->where('id', '!=', $auth->id)
                ->first();

            if ($partner) {
                $calendar->user = $auth;
                $partner->notify(new CalendarEventCreated($calendar));
            }

            DB::commit();
            return redirect()->route('calendar.index')->with('success', '予定を追加しました！');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', '予定の登録中にエラーが発生しました。')->withInput();
        }
    }

    /**
     * 編集
     */
    public function edit($id, Request $request)
    {
        $auth  = $request->user();
        $event = Calendar::where('id', $id)
            ->where('family_id', $auth->family_id)
            ->firstOrFail();

        return view('calendar.edit', compact('event'));
    }

    /**
     * 更新（秒なしで保存）
     */
    public function update(CalendarRequest $request, $id)
    {
        $auth  = $request->user();
        $event = Calendar::where('id', $id)
            ->where('family_id', $auth->family_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['event_time'] = $data['event_time'] ?: null;

            $event->update($data);

            DB::commit();
            return redirect()->route('calendar.index')->with('success', '予定を更新しました！');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', '予定の更新に失敗しました。')->withInput();
        }
    }

    /**
     * 削除
     */
    public function destroy($id, Request $request)
    {
        $auth  = $request->user();
        $event = Calendar::where('id', $id)
            ->where('family_id', $auth->family_id)
            ->firstOrFail();

        $event->delete();

        return redirect()->route('calendar.index')->with('success', '予定を削除しました！');
    }
}
