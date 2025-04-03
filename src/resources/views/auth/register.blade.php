@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">ユーザー登録</h2>
    @include('partials.alerts')

    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">パスワード（確認）</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary w-50 mt-3 mb-5">登録</button>
            <a href="{{ route('login') }}" class="btn btn-secondary w-50 mt-3 mb-5">戻る</a>
        </div>        
    </form>
</div>
@endsection
