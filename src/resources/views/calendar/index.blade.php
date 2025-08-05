@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2 class="my-3">カレンダー</h2>

    <!-- FullCalendar 表示 -->
    <div id="calendar" style="min-height: 600px;" class="mb-4"></div>

    <!-- アラートメッセージ -->
    @include('partials.alerts')

    <!-- 予定追加フォーム（従来版：オプションで残す） -->
    <div class="card p-3 mb-4 d-none d-md-block">
        <form action="{{ route('calendar.store') }}" method="POST" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
                <input type="text" name="title" class="form-control" placeholder="予定のタイトル" required>
            </div>
            <div class="col-md-2">
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input type="time" name="event_time" class="form-control">
            </div>
            <div class="col-md-3">
                <textarea name="description" class="form-control" rows="1" placeholder="メモ（任意）">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">追加</button>
            </div>
        </form>
    </div>

    <!-- モバイル向け予定追加モーダル -->
    <div class="modal fade" id="calendarAddModal" tabindex="-1" aria-labelledby="calendarAddModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('calendar.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="calendarAddModalLabel">予定を追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                <label class="form-label">タイトル</label>
                <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                <label class="form-label">日付</label>
                <input type="date" name="event_date" class="form-control" id="modal-event-date" required>
                </div>
                <div class="mb-3">
                <label class="form-label">時間</label>
                <input type="time" name="event_time" class="form-control">
                </div>
                <div class="mb-3">
                <label class="form-label">メモ</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">追加</button>
            </div>
            </form>
        </div>
    </div>

    <!-- 今月の予定リスト -->
    <h3 class="mt-4">予定リスト</h3>
    <div id="event-list-area"></div>

    <div class="d-flex justify-content-center mt-4">
        <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
    </div>
</div>

<!-- FullCalendar CSS/JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Laravel → JS 渡し -->
<script>
    const allEvents = @json($events);
    const authUserId = {{ auth()->id() }};
</script>

<!-- FullCalendar 設定 -->
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
            filtered.forEach(event => {
                const date = new Date(event.event_date);
                const formattedDate = `${date.getMonth() + 1}/${date.getDate()}`;
                const time = event.event_time ?? '--:--';

                listArea.insertAdjacentHTML('beforeend', `
                    <div class="border border-warning rounded bg-warning-subtle p-2 mb-2">
                        <div class="fw-bold">${event.title}</div>
                        <small class="text-muted">日付：${formattedDate}</small><br>
                        <small class="text-muted">時間：${time}</small><br>
                        <small class="text-muted">メモ：${event.description ?? ''}</small><br>
                        <div class="mt-2 d-flex gap-2">
                            <a href="/calendar/${event.id}/edit" class="btn btn-sm btn-warning">編集</a>
                            <form method="POST" action="/calendar/${event.id}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </div>
                    </div>
                `);
            });
        } else {
            let html = `<div class="table-responsive"><table class="table table-striped">
                    <thead><tr><th>タイトル</th><th>日付</th><th>時間</th><th>メモ</th><th>操作</th></tr></thead><tbody>`;

            filtered.forEach(event => {
                const date = new Date(event.event_date);
                const formattedDate = `${date.getMonth() + 1}/${date.getDate()}`;
                const time = event.event_time ?? '--:--';

                html += `
                    <tr>
                        <td>${event.title}</td>
                        <td>${formattedDate}</td>
                        <td>${time}</td>
                        <td>${event.description ?? ''}</td>
                        <td>
                            <a href="/calendar/${event.id}/edit" class="btn btn-sm btn-warning">編集</a>
                            <form method="POST" action="/calendar/${event.id}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>`;
            });

            html += '</tbody></table></div>';
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
                color: e.user_id === authUserId ? '#0d6efd' : '#ffc107'
            })),
            displayEventTime: false,
            eventDisplay: 'block',
            dateClick: function(info) {
                const modal = new bootstrap.Modal(document.getElementById('calendarAddModal'));
                document.getElementById('modal-event-date').value = info.dateStr;
                modal.show();
            },
            datesSet: function() {
                renderEventList(allEvents, calendar.getDate());
            }
        });
        calendar.render();
        renderEventList(allEvents, calendar.getDate());
    });
</script>
@endsection
