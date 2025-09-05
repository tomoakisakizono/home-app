@extends('layouts.app')

@section('content')
@php
  $dow = ['日','月','火','水','木','金','土'];
@endphp

<meta name="calendar-index-url" content="{{ route('calendar.index') }}">

<style>
  /* FullCalendar 内の表示を少し見やすく（任意） */
  .fc .hc-pill{display:inline-flex;align-items:center;gap:.25rem;padding:0 .375rem;border-radius:9999px;background:#eef4ff;}
  .fc .hc-time{font-size:.75rem;opacity:.85;}
  .fc .hc-title{font-size:.75rem;}

  /* ✕ボタン：灰色・中央配置・ホバー可 */
  .hc-xbtn{
    width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;
    border:1px solid #c8cfd6;border-radius:.5rem;background:#fff;color:#6c757d;
    padding:0;line-height:1;cursor:pointer;position:relative;z-index:3; /* ← 重要 */
  }
  .hc-xbtn:hover{background:#f1f3f5;color:#495057;border-color:#b8c0c8;}

  /* カード全体クリックで編集へ（JS不要・inline onclick） */
  .event-card{position:relative;cursor:pointer;}
</style>

<div class="container mb-4" style="max-width:980px;">
  <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
    <h2 class="my-0">カレンダー</h2>
  </div>

  {{-- FullCalendar --}}
  <div id="calendar" class="mb-4" style="min-height:560px;"></div>

  {{-- 右下の＋（data属性でモーダル起動） --}}
  <button id="fab-add" class="btn btn-primary hc-fab" aria-label="予定を追加"
          data-bs-toggle="modal" data-bs-target="#eventModal">
    <i class="bi bi-plus-lg fs-4"></i>
  </button>

  {{-- 予定追加モーダル --}}
  <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="eventForm" class="modal-content" method="POST" action="{{ route('calendar.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">予定を追加</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label">タイトル</label>
            <input name="title" class="form-control" placeholder="例：保育園送り" required value="{{ old('title') }}">
          </div>
          <div class="col-6">
            <label class="form-label">日付</label>
            <input id="modal-date" type="date" name="event_date" class="form-control" required
                   value="{{ old('event_date', $selected ?? now()->format('Y-m-d')) }}">
          </div>
          <div class="col-6">
            <label class="form-label">時間</label>
            <input type="time" name="event_time" class="form-control" value="{{ old('event_time') }}">
          </div>
          <div class="col-12">
            <label class="form-label">メモ（任意）</label>
            <textarea name="description" class="form-control" rows="2" placeholder="補足や場所など">{{ old('description') }}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary w-100" type="submit">追加</button>
        </div>
      </form>
    </div>
  </div>

  {{-- 予定リスト --}}
  <h3 class="mt-4">予定リスト</h3>
  <ul class="nav nav-tabs mb-2">
    <li class="nav-item">
      <button class="nav-link {{ $selected ? '' : 'active' }}" id="tab-month" data-bs-toggle="tab" data-bs-target="#pane-month" type="button">
        今月の予定
        <span id="month-count" class="badge bg-secondary ms-1">
          {{ $eventsByDate->flatten(1)->count() }}
        </span>
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link {{ $selected ? 'active' : '' }}" id="tab-day" data-bs-toggle="tab" data-bs-target="#pane-day" type="button">
        選択日の予定
        @if($selected)
          @php $d=\Carbon\Carbon::parse($selected); @endphp
          <span id="day-label" class="text-muted small ms-1">
            {{ $d->format('n/j') }}（{{ $dow[$d->dayOfWeek] }}）
          </span>
        @else
          <span id="day-label" class="text-muted small ms-1"></span>
        @endif
      </button>
    </li>
  </ul>

  <div class="tab-content">
    {{-- 今月の予定 --}}
    <div class="tab-pane fade {{ $selected ? '' : 'show active' }}" id="pane-month">
      <div id="month-list">
        @if($eventsByDate->isEmpty())
          <div class="text-muted">この月の予定はありません。</div>
        @else
          @foreach($eventsByDate->keys()->sort() as $date)
            @php $d=\Carbon\Carbon::parse($date); @endphp
            <h6 class="mt-3 mb-2">{{ $d->format('n月j日') }}（{{ $dow[$d->dayOfWeek] }}）</h6>

            @foreach($eventsByDate[$date]->sortBy([['event_time','asc']]) as $ev)
              @php $time = $ev->event_time_hi; @endphp
              <div class="card mb-2 shadow-sm event-card"
                   role="button"
                   onclick="if(!event.target.closest('form') && !event.target.closest('button')){ window.location.href='{{ route('calendar.edit', $ev->id) }}'; }">
                <div class="card-body py-2 d-flex justify-content-between align-items-start">
                  <div class="pe-2">
                    <div class="fw-semibold">{{ $ev->title }}</div>
                    <div class="text-muted small mt-1">{{ $time ?? '--:--' }}　{{ $ev->description }}</div>
                  </div>
                  <form method="POST" action="{{ route('calendar.destroy', $ev->id) }}"
                        onsubmit="event.stopPropagation(); return confirm('この予定を削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="hc-xbtn" aria-label="削除" title="削除"
                            onclick="event.stopPropagation();">×</button>
                  </form>
                </div>
              </div>
            @endforeach
          @endforeach
        @endif
      </div>
    </div>

    {{-- 選択日の予定 --}}
    <div class="tab-pane fade {{ $selected ? 'show active' : '' }}" id="pane-day">
      <div id="day-list">
        @if(!$selected)
          <div class="text-muted">日付をクリックすると、この日の予定が表示されます。</div>
        @elseif($dayEvents->isEmpty())
          <div class="text-muted">この日の予定はありません。</div>
        @else
          @foreach($dayEvents as $ev)
            @php $time = $ev->event_time_hi; @endphp
            <div class="card mb-2 shadow-sm event-card"
                 role="button"
                 onclick="if(!event.target.closest('form') && !event.target.closest('button')){ window.location.href='{{ route('calendar.edit', $ev->id) }}'; }">
              <div class="card-body py-2 d-flex justify-content-between align-items-start">
                <div class="pe-2">
                  <div class="fw-semibold">{{ $ev->title }}</div>
                  <div class="text-muted small mt-1">{{ $time ?? '--:--' }}　{{ $ev->description }}</div>
                </div>
                <form method="POST" action="{{ route('calendar.destroy', $ev->id) }}"
                      onsubmit="event.stopPropagation(); return confirm('この予定を削除しますか？');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="hc-xbtn" aria-label="削除" title="削除"
                          onclick="event.stopPropagation();">×</button>
                </form>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

{{-- FullCalendar に渡すデータ --}}
<script>
  window.CalendarData = {
    events: @json($fcEvents),
  };
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection
