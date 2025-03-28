@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2 class="text-center my-3">メッセージ</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- 🔹 投稿フォーム -->
    <div class="card p-3 mb-3">
        <form action="{{ route('messages.store') }}" method="POST">
            @csrf
            <div class="row g-2 align-items-start">
                <div class="col-12 col-md-10">
                    <input type="text" class="form-control" name="content" placeholder="メッセージを入力" required>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary w-100">投稿</button>
                </div>
            </div>

            <!-- 🔸 カレンダー連携 -->
            <div class="mt-3">
                <h5 class="mb-2">カレンダー連携</h5>
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <input type="date" class="form-control" name="event_date" placeholder="日付">
                    </div>
                    <div class="col-6 col-md-2">
                        <input type="time" class="form-control" name="event_time" placeholder="時間">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="text" class="form-control" name="event_title" placeholder="予定タイトル">
                    </div>
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control" name="event_description" placeholder="詳細（任意）">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 🔹 メッセージ一覧 -->
    @foreach($messages as $message)
        <div class="border border-light rounded bg-light p-3 mb-3">
            <div class="fw-bold mb-1">{{ $message->user->name }}：</div>
            <div class="p-2 rounded bg-success-subtle">{{ $message->content }}</div>

            @if($message->calendar)
                <div class="text-success small mt-2">
                    📅 {{ \Carbon\Carbon::parse($message->calendar->event_date)->format('n/j') }}
                    @if($message->calendar->event_time)
                        {{ \Carbon\Carbon::parse($message->calendar->event_time)->format('H:i') }}
                    @endif
                    ：{{ $message->calendar->title }}
                    @if($message->calendar->description)
                        （{{ $message->calendar->description }}）
                    @endif
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>

                @if($message->user_id === auth()->id())
                    <div class="d-flex gap-2">
                        <a href="{{ route('messages.edit', $message->id) }}" class="btn btn-sm btn-warning">編集</a>
                        <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">削除</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
<div class="d-flex justify-content-center mt-3 mb-3">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary">ペアページへ</a>
</div>
@endsection
