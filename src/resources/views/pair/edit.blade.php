@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="text-center mb-3 col-md-3">
        <img src="{{ $pair->getImageUrl() }}" class="img-fluid rounded-circle" style="width: 250px; height: 250px; object-fit: cover;" alt="ペア画像">
    </div>

    <div class="col-md-9 mt-4">
        <div class="d-flex justify-content-between">
            <!-- 自分の情報 -->
            <div class="card p-3 text-left">
                <h5 class="pair-info">ユーザーネーム:<br> {{ $user->name }}</h5>
                <h5 class="pair-info">アドレス:<br> {{ $user->email }}</h5>
                <h5 class="pair-info">
                    ペアネーム:<br>
                    @if (!empty($partner))
                        {{ $pair->pair_name }}
                    @else
                        ペア未設定
                    @endif
                </h5>
            </div>

            <div class="align-self-center">
                <h2>⇆</h2>
            </div>

            <!-- ペアの相手の情報 -->
            <div class="card p-3">
                @if (!empty($partner))
                <h5 class="pair-info">ユーザーネーム:<br> {{ $partner->name }}</h5>
                <h5 class="pair-info">アドレス:<br> {{ $partner->email }}</h5>
                <h5 class="pair-info">ペアネーム:<br>{{ $pair->pair_name }}</h5>
                @else
                    <h5>ペアが未設定です</h5>
                    <p>招待コードを入力してください。</p>
                @endif
            </div>
        </div>
    </div>
</div>

<h4 class="text-center mb-3">ペア編集</h4>

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