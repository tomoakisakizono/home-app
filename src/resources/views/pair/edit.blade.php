@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<div class="row justify-content-center align-items-center my-3">
    <div class="text-center mb-3 col-md-3">
        <img src="{{ $pair->getImageUrl() }}" class="img-fluid rounded-circle" style="width: 250px; height: 250px; object-fit: cover;" alt="ペア画像">
    </div>
    <!-- 自分 -->
    <div class="col-md-4 text-center">
        <div class="profile-card p-4 border rounded-3 bg-white shadow-sm">
            <img src="{{ asset('storage/' . $user->profile_image) }}"
                class="mb-3 border border-2"
                style="width: 120px; height: 120px; object-fit: cover;">
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <div class="text-muted small">{{ $user->email }}</div>
        </div>
    </div>

    <!-- 中央のペアアイコン -->
    <div class="col-md-1 d-flex flex-column align-items-center justify-content-center">
        <div class="fw-light small text-muted mt-1">⇆Pair⇆</div>
    </div>

    <!-- 相手 -->
    <div class="col-md-4 text-center">
        <div class="profile-card p-4 border rounded-3 bg-white shadow-sm">
            @if (!empty($partner))
                <img src="{{ asset('storage/' . $partner->profile_image) }}"
                    class="mb-3 border border-2"
                    style="width: 120px; height: 120px; object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $partner->name }}</h5>
                <div class="text-muted small">{{ $partner->email }}</div>
            @else
                <p class="text-danger mt-4">ペア未設定</p>
            @endif
        </div>
    </div>
</div>
<!-- ペアネーム表示 -->
@if (!empty($partner))
    <div class="text-center mb-4">
        <span class="badge bg-dark px-4 py-2 fs-6">ペアネーム：{{ $pair->pair_name }}</span>
    </div>
@endif

<h4 class="text-center mb-3 mt-2">ペア編集</h4>

<!-- ペアネーム編集フォーム -->
<form action="{{ route('pair.update_name') }}" method="POST">
    @csrf
    <label for="pair_name">ペアネーム:</label>
    <input type="text" class="form-control" name="pair_name" value="{{ $pair->pair_name }}" required maxlength="50">
    <button type="submit" class="btn btn-primary mt-2 mb-4">保存</button>
</form>

<!-- ペア画像編集フォーム -->
<form action="{{ route('pair.update_image') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="pair_image" class="form-label">ペア画像をアップロード</label>
        <input type="file" class="form-control" name="pair_image" id="pair_image" required>
    </div>
    <button type="submit" class="btn btn-primary mb-4">画像を更新</button>
</form>
@endsection