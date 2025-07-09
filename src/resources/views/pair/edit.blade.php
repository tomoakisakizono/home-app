@extends('layouts.app')

@section('content')

<h4 class="text-center my-3">ペア編集</h4>
@include('partials.alerts')

<div class="row justify-content-center align-items-center my-3">
    <div class="text-center mb-3 col-md-3">
        <img src="{{ $pair->getImageUrl() }}" class="img-fluid rounded-circle" style="width: 250px; height: 250px; object-fit: cover;" alt="ペア画像">
    </div>
</div>

<!-- ペアネーム表示 -->
@if (!empty($partner))
    <div class="text-center mb-4">
        <span class="badge bg-dark px-4 py-2 fs-6">ペアネーム：{{ $pair->pair_name }}</span>
    </div>
@endif

<!-- ペア画像編集フォーム -->
<form action="{{ route('pair.update_image') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="pair_image" class="form-label">ペア画像をアップロード</label>
        <input type="file" class="form-control" name="pair_image" id="pair_image" required>
    </div>
    <button type="submit" class="btn btn-primary mb-4">画像を更新</button>
</form>

<!-- ペアネーム編集フォーム -->
<form action="{{ route('pair.update_name') }}" method="POST">
    @csrf
    <label for="pair_name">ペアネーム:</label>
    <input type="text" class="form-control" name="pair_name" value="{{ $pair->pair_name }}" required maxlength="50">
    <button type="submit" class="btn btn-primary mt-3 mb-4">保存</button>
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mt-3 mb-4">戻る</a>
</form>
@endsection