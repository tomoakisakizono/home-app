@extends('layouts.app')

@section('content')

<div class="container mb-4">
    <h2>作業リスト</h2>
    @include('partials.alerts')

    {{-- 作業追加フォーム --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label for="title" class="form-label">作業名</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="col-md-4">
                        <label for="due_date" class="form-label">期限</label>
                        <input type="date" class="form-control" id="due_date" name="due_date"
                            value="{{ old('due_date', today()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-primary w-100">追加</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 作業リスト --}}
    @if ($tasks->isEmpty())
        <p>やるべき作業はありません。</p>
    @else
        @foreach ($tasks as $month => $taskGroup)
            <h4 class="mt-4">{{ $month }}</h4>
            <ul class="list-group mb-3">
                @foreach ($taskGroup as $task)
                <li class="list-group-item {{ $task->is_done ? 'list-group-item-success' : 'list-group-item-warning' }}">
                        <div class="d-flex flex-column gap-2">
                            {{-- 上段：内容 --}}
                            <div>
                                <strong class="d-inline-block">
                                    {{ $task->title }}
                                    @if ($task->is_due_soon && !$task->is_done)
                                        <span class="badge bg-warning text-dark ms-2">⚠ 期限間近</span>
                                    @endif
                                </strong>
                                <div class="text-muted small">期限: {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}</div>
                            </div>

                            {{-- 下段：操作ボタン --}}
                            <div class="d-flex gap-2 flex-wrap">
                                <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $task->is_done ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $task->is_done ? '完了' : '未完了' }}
                                    </button>
                                </form>

                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">編集</a>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">削除</button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endforeach
    @endif
</div>
<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>
@endsection
