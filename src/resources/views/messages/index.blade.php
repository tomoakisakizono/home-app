@extends('layouts.app')

@section('content')
<h2 class="text-center">メッセージ一覧</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- 🔹 メッセージ投稿フォーム -->
<form action="{{ route('messages.store') }}" method="POST">
    @csrf
    <div class="d-flex">
        <textarea class="form-control me-2" name="content" rows="1" required placeholder="メッセージを入力"></textarea>
        <button type="submit" class="btn btn-primary">投稿</button>
    </div>

    <!-- 🔹 カレンダー連携フォーム -->
    <div class="mt-3">
        <h5>カレンダー連携（オプション）</h5>
        <div class="d-flex">
            <input type="date" class="form-control me-2" name="event_date" placeholder="日付を選択">
            <input type="time" class="form-control me-2" name="event_time" placeholder="時間を選択">
            <input type="text" class="form-control me-2" name="event_title" placeholder="予定タイトル">
            <input type="text" class="form-control me-2" name="event_description" placeholder="詳細">
        </div>
    </div>
</form>

<!-- 🔹 メッセージ一覧 -->
<ul class="list-group mt-4">
    @foreach($messages as $message)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $message->user->name }}</strong>: {{ $message->content }}
                <br><small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>

                <!-- 🔹 カレンダー情報表示（予定がある場合） -->
                @if($message->calendar)
                    <br>
                    <small class="text-success">
                        📅 <a href="{{ route('calendar.show', $message->calendar->id) }}">
                            {{ $message->calendar->event_date }} 
                            @if($message->calendar->event_time)
                                {{ $message->calendar->event_time }}
                            @endif
                            : {{ $message->calendar->title }}
                        </a>
                    </small>
                @endif
            </div>

            <!-- 🔹 投稿者のみ編集・削除可能 -->
            @if($message->user_id === auth()->id())
                <div>
                    <a href="{{ route('messages.edit', $message->id) }}" class="btn btn-sm btn-warning">編集</a>
                    <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">削除</button>
                    </form>
                </div>
            @endif
        </li>
    @endforeach
</ul>
@endsection
