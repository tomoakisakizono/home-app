@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">カテゴリー管理</h2>
    <a href="{{ route('shopping.index') }}" class="btn btn-secondary">
        買い物リストに戻る
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- 🔹 カテゴリー追加フォーム -->
<form action="{{ route('categories.store') }}" method="POST" class="d-flex mb-3">
    @csrf
    <input type="text" name="name" class="form-control me-2" placeholder="カテゴリー名" required>
    <button type="submit" class="btn btn-primary">追加</button>
</form>

<!-- 🔹 カテゴリー一覧表示 -->
<ul class="list-group mb-4">
    @foreach($categories as $category)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $category->name }}
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('このカテゴリーを削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">削除</button>
            </form>
        </li>
    @endforeach
</ul>
<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-1">ペアページへ</a>
</div>

@endsection
