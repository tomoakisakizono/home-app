@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <!-- カレンダー表示 -->
    <div id="calendar" style="min-height: 600px;" class="mb-4"></div>
    @include('partials.alerts')

    <!-- 予定追加フォーム -->
    <div class="card p-3 mb-4">
        <form action="{{ route('calendar.store') }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-12 col-md-3">
                <input type="text" name="title" class="form-control" placeholder="予定のタイトル" required>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="col-6 col-md-2">
                <input type="time" name="event_time" class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <textarea name="description" class="form-control" rows="1" placeholder="メモ（任意）">{{ old('description') }}</textarea>
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">追加</button>
            </div>
        </form>
    </div>

    <!-- 予定リスト -->
    <h3 class="mt-4">予定リスト</h3>
    <div id="event-list-area"></div>
</div>
<div class="d-flex justify-content-center mt-4">
        <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>

<!-- FullCalendar CSS・JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<!-- LaravelイベントデータをJSに渡す -->
<script>
    const allEvents = @json($events);
</script>

<script>
    function isMobile() {
        return window.innerWidth < 768;
    }

    function renderEventList(events, currentMonth) {
        const listArea = document.getElementById('event-list-area');
        listArea.innerHTML = '';

        const filtered = events.filter(e => {
            const date = new Date(e.event_date);
            return date.getFullYear() === currentMonth.getFullYear() &&
                date.getMonth() === currentMonth.getMonth();
        });

        if (filtered.length === 0) {
            listArea.innerHTML = '<p>予定はありません。</p>';
            return;
        }

        if (isMobile()) {
            // スマホ表示（カード）
            filtered.forEach(event => {
                const dateObj = new Date(event.event_date);
                const formattedDate = `${dateObj.getMonth() + 1}/${dateObj.getDate()}`;

                const timeObj = event.event_time ? new Date(`1970-01-01T${event.event_time}`) : null;
                const formattedTime = timeObj
                    ? `${timeObj.getHours().toString().padStart(2, '0')}:${timeObj.getMinutes().toString().padStart(2, '0')}`
                    : '--:--';

                const html = `
                    <div class="border border-warning rounded bg-warning-subtle p-2 mb-2">
                        <div class="fw-bold">${event.title}</div>
                        <small class="text-muted">日付：${formattedDate}</small><br>
                        <small class="text-muted">時間：${formattedTime}</small><br>
                        <small class="text-muted">メモ：${event.description ? `${event.description}` : ''}</small><br>
                        <div class="mt-2 d-flex gap-2">
                            <a href="/calendar/${event.id}/edit" class="btn btn-sm btn-warning">編集</a>
                            <form method="POST" action="/calendar/${event.id}" class="d-inline">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </div>
                    </div>
                `;
                listArea.insertAdjacentHTML('beforeend', html);
            });
        } else {
            // PC表示（表形式）
            let html = `
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>日付</th>
                            <th>時間</th>
                            <th>メモ</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            filtered.forEach(event => {
                const dateObj = new Date(event.event_date);
                const formattedDate = `${dateObj.getMonth() + 1}/${dateObj.getDate()}`;

                const timeObj = event.event_time ? new Date(`1970-01-01T${event.event_time}`) : null;
                const formattedTime = timeObj
                    ? `${timeObj.getHours().toString().padStart(2, '0')}:${timeObj.getMinutes().toString().padStart(2, '0')}`
                    : '--:--';

                html += `
                    <tr>
                        <td>${event.title}</td>
                        <td>${formattedDate}</td>
                        <td>${formattedTime}</td>
                        <td>${event.description ?? ''}</td>
                        <td>
                            <a href="/calendar/${event.id}/edit" class="btn btn-sm btn-warning">編集</a>
                            <form method="POST" action="/calendar/${event.id}" style="display:inline;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            listArea.innerHTML = html;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: allEvents.map(e => ({
                title: e.title,
                start: e.event_date,
                color: '#28a745'
            })),
            displayEventTime: false,
            eventDisplay: 'block',
            datesSet: function(info) {
                const currentDate = calendar.getDate(); // ← ここで正確な表示月取得
                renderEventList(allEvents, currentDate);
            }
        });
        calendar.render();
        renderEventList(allEvents, calendar.getDate()); // 初期表示
    });
</script>
@endsection
