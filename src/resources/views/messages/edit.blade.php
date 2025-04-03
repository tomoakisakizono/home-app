@extends('layouts.app')

@section('content')
<div class="container mb-3">
    <h2>メッセージ編集</h2>
    @include('partials.alerts')

    <form action="{{ route('messages.update', $message->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="content" class="form-label">メッセージ内容</label>
            <textarea name="content" id="content" class="form-control" rows="3" required>{{ old('content', $message->content) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('messages.index') }}" class="btn btn-secondary">キャンセル</a>
    </form>
</div>
@endsection
