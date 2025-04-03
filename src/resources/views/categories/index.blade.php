@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">カテゴリー管理</h2>
    <a href="{{ route('shopping.index') }}" class="btn btn-secondary">
        買い物リストに戻る
    </a>
</div>
@include('partials.alerts')

<!-- 🔹 カテゴリー追加フォーム -->
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <div class="row g-2 align-items-start">
        <div class="col-12 col-md-10">
            <input type="text" name="name" class="form-control me-2" placeholder="カテゴリー名" required>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">追加</button>
        </div>
    </div>
</form>

<!-- 🔹 カテゴリー一覧表示 -->
<ul class="list-group mt-4 mb-4">
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
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>

@endsection
