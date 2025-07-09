@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<style>
    .login-screen {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-image: url('{{ asset('images/main-visual.jpg') }}');
    background-size: cover;
    background-position: center;
    padding: 3rem 5%;
    }
</style>

<div class="login-screen">
    <div class="intro-box text-white">
        <div class="row">
            <div class="col-12">
                <div class="box" style="position: absolute; top: 0; left: 0; padding: 10px;">
                    <h1 class="fw-bold">ホームコミュニケーションへようこそ</h1>
                    <p>このアプリは、家族やパートナーとの日々の連携を効率化し、<br>
                        心の距離を縮めることを目的としたコミュニケーションツールです。
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" style="flex: 1; display: flex; align-items: center;">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
            <div class="login-box mt-5 p-3 bg-body bg-opacity-25 text-white">
                <h3 class="text-center">ログイン</h3>
                <p class="small mb-4">※アカウントをお持ちでない方は、画面右上の新規ユーザ登録してください</p>

                @include('partials.alerts')        

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">パスワード</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">ログイン</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
