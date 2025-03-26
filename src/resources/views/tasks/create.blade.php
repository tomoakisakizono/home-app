@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>作業を追加</h2>

    {{-- バリデーションエラー --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">作業内容</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">期限日</label>
            <input type="date" name="due_date" id="due_date" class="form-control"
                value="{{ old('due_date', today()->format('Y-m-d')) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">登録する</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">戻る</a>
    </form>
</div>
@endsection
