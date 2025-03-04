@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">ログイン</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <button type="submit" class="btn btn-primary">ログイン</button>
            <a href="{{ route('home') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection
