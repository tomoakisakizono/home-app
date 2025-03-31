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

    {{-- 月切り替えナビゲーション --}}
    @php
        use Carbon\Carbon;
        $current = Carbon::createFromFormat('Y-m', $selectedMonth);
        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('tasks.index', ['month' => $prevMonth]) }}" class="btn btn-outline-secondary">&lt; 前の月</a>
        <h4 class="mb-0">{{ $current->format('Y年m月') }}</h4>
        <a href="{{ route('tasks.index', ['month' => $nextMonth]) }}" class="btn btn-outline-secondary">次の月 &gt;</a>
    </div>

    {{-- 作業リスト --}}
    @if ($tasks->isEmpty())
        <p>この月には作業が登録されていません。</p>
    @else
        <ul class="list-group">
        @foreach ($tasks as $task)
                <li class="list-group-item
                    {{ $task->is_done ? 'list-group-item-success' : 'list-group-item-warning' }}">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 flex-wrap">

                        {{-- タスク内容 --}}
                        <div class="flex-grow-1">
                            <strong class="d-inline-block mb-1">
                                {{ $task->title }}
                                @if ($task->is_due_soon && !$task->is_done)
                                    <span class="badge bg-warning text-dark ms-2">⚠ 期限間近</span>
                                @endif
                            </strong>
                            <div class="text-muted small">期限: {{ \Carbon\Carbon::parse($task->due_date)->format('Y/m/d') }}</div>
                        </div>

                        {{-- 操作ボタン --}}
                        <div class="d-flex gap-2 flex-wrap">
                            {{-- 完了状態トグル --}}
                            <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $task->is_done ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $task->is_done ? '完了' : '未完了' }}
                                </button>
                            </form>

                            {{-- 編集 --}}
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">編集</a>

                            {{-- 削除 --}}
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </div>

                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>
@endsection
