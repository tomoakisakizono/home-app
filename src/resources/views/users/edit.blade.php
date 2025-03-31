@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">プロフィール編集</h2>
    @include('partials.alerts')

    <div class="row justify-content-center align-items-center my-3">
        <!-- 自分 -->
        <div class="col-md-4 text-center">
            <div class="profile-card p-4 border rounded-3 bg-white shadow-sm">
                <img src="{{ asset('storage/' . $user->profile_image) }}"
                    class="rounded-circle mb-3 border border-2"
                    style="width: 120px; height: 120px; object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <div class="text-muted small">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    {{-- プロフィール画像 --}}
    <form action="{{ route('users.updateImage') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="profile_image" class="form-label">プロフィール画像</label>
            <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">画像を更新</button>
    </form>

    {{-- ユーザー情報編集 --}}
    <form action="{{ route('users.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">ユーザーネーム</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">新しいパスワード（変更しない場合は空欄）</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">パスワード確認</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
        </div>

        <button type="submit" class="btn btn-success mb-3">プロフィールを更新</button>
        <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">戻る</a>
    </form>
</div>
@endsection
