@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>買い物リスト</h2>
    <!-- 🔹 カテゴリー管理ページへのリンクボタン -->
    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">カテゴリー管理</a>
</div>
@include('partials.alerts')

<!-- 🔹 買い物リスト追加フォーム -->
<form action="{{ route('shopping.store') }}" method="POST" class="d-flex flex-wrap align-items-center">
    @csrf
    <input type="text" name="item_name" class="form-control me-2 mb-2" placeholder="アイテム名" required>
    <input type="number" name="quantity" class="form-control me-2 mb-2" min="1" placeholder="個数" required>

    <!-- 🔹 カテゴリー選択 -->
    <select name="category_id" class="form-select me-2 mb-2">
        <option value="">カテゴリーなし</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-primary mb-2">追加</button>
</form>

<!-- 🔹 カテゴリーごとにリストを表示 -->
@foreach($shoppingLists as $categoryName => $items)
    <h3 class="mt-4">{{ $categoryName ?? 'その他' }}</h3>
    <ul class="list-group mb-4">
        @foreach($items as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                    {{ $item->item_name }} ({{ $item->quantity }}個)
                    @if (!empty($item->category))
                        <span class="badge bg-info">{{ optional($item->category)->name }}</span>
                    @endif
                </span>
                <div>
                    <!-- 🔹 購入済みボタン -->
                    <button class="btn btn-sm {{ $item->status === '購入済み' ? 'btn-success' : 'btn-secondary' }} update-status me-2"
                        data-id="{{ $item->id }}">
                        {{ $item->status }}
                    </button>

                    <!-- 🔹 削除ボタン -->
                    <form action="{{ route('shopping.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">削除</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endforeach

<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".update-status").forEach(button => {
        button.addEventListener("click", function() {
            let itemId = this.dataset.id;
            let button = this;

            fetch(`/shopping/${itemId}/status`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = data.newStatus;
                    button.classList.toggle("btn-success");
                    button.classList.toggle("btn-secondary");
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});
</script>

@endsection
