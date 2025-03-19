@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2 class="text-center my-3"><i class="fa-regular fa-calendar"></i> カレンダー</h2>

    <!-- カレンダー表示 -->
    <div id="calendar" class="mb-4"></div>

    <!-- 予定追加フォーム -->
    <div class="card p-3">
        <form action="{{ route('calendar.store') }}" method="POST" class="row g-2">
            @csrf
            <div class="col-12 col-md-4">
                <input type="text" name="title" class="form-control" placeholder="予定のタイトル" required>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="col-6 col-md-3">
                <input type="time" name="event_time" class="form-control">
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">追加</button>
            </div>
        </form>
    </div>

    <!-- 予定リスト表示 -->
    <h3 class="mt-4">予定リスト</h3>
    <!-- 📌 予定リストをスマホとPCで異なる表示にする -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-striped mt-2">
            <thead>
                <tr>
                    <th style="width: 30%;">タイトル</th>
                    <th style="width: 25%;">日付</th>
                    <th style="width: 15%;">時間</th>
                    <th style="width: 30%;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</td>
                        <td>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '--:--' }}</td>
                        <td>
                            <a href="{{ route('calendar.edit', $event->id) }}" class="btn btn-sm btn-warning">編集</a>
                            <form action="{{ route('calendar.destroy', $event->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 📌 スマホ表示ではカード形式 -->
    <div class="d-md-none">
        @foreach($events as $event)
            <div class="card p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $event->title }}</strong><br>
                        <small>日付: {{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}</small><br>
                        <small>時間: {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '--:--' }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('calendar.edit', $event->id) }}" class="btn btn-sm btn-warning me-2">編集</a>
                        <form action="{{ route('calendar.destroy', $event->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">削除</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- FullCalendar のスタイルとスクリプト -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            events: [
                @foreach($events as $event)
                {
                    title: "{{ $event->title }}",
                    start: "{{ $event->event_date }}",
                    color: "#ff9f89" // 🔹 イベントの日に色を付ける
                },
                @endforeach
            ]
        });
        calendar.render();
    });
</script>

<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-1">ペアページへ</a>
</div>

@endsection
