@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>作業を編集</h2>
    @include('partials.alerts')

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">作業内容</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">期限日</label>
            <input type="date" name="due_date" id="due_date" class="form-control"
                value="{{ old('due_date', $task->due_date) }}" required>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" name="is_done" id="is_done" value="1" {{ $task->is_done ? 'checked' : '' }}>
            <label class="form-check-label" for="is_done">完了済みにする</label>
        </div>

        <button type="submit" class="btn btn-success">更新する</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">戻る</a>
    </form>
</div>
@endsection
