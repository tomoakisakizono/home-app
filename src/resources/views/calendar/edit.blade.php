@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2 class="my-4"><i class="fa-regular fa-pen-to-square"></i> 予定を編集</h2>
    @include('partials.alerts')

    <div class="card p-3">
        <form action="{{ route('calendar.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">予定タイトル</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $event->title) }}" required>
            </div>

            <div class="mb-3">
                <label for="event_date" class="form-label">日付</label>
                <input type="date" name="event_date" id="event_date" class="form-control" value="{{ old('event_date', $event->event_date) }}" required>
            </div>

            <div class="mb-3">
                <label for="event_time" class="form-label">時間</label>
                <input type="time" name="event_time" id="event_time" class="form-control" value="{{ old('event_time', $event->event_time) }}">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">説明（任意）</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">更新する</button>
                <a href="{{ route('calendar.index') }}" class="btn btn-secondary">戻る</a>
            </div>
        </form>
    </div>
</div>
@endsection
