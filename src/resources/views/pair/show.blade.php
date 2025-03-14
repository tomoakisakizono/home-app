@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-md-3">
        <img src="https://via.placeholder.com/150" class="img-fluid" alt="ユーザー画像">
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between">
            <!-- 自分の情報 -->
            <div class="card p-3 text-left">
                <h5 class="card-title">ユーザーネーム:<br> {{ $user->name }}</h5>
                <h5 class="card-body">アドレス:<br> {{ $user->email }}</h5>
                <h5 class="card-body">
                    ペアネーム:<br>
                    @if (!empty($partner))
                        {{ $partner->name }}とペア
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
                <h5 class="card-title">ユーザーネーム:<br> {{ $partner->name }}</h5>
                <h5 class="card-body">アドレス:<br> {{ $partner->email }}</p>
                <h5 class="card-body">ペアネーム:<br> {{ $user->name }}とペア</p>
                @else
                    <h5>ペアが未設定です</h5>
                    <p>招待コードを入力してください。</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">プルダウンボタン</button>
            <button class="btn btn-secondary">プレースホルダ</button>
        </div>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>機能</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>機能</td>
                    <td>テキストテキストテキスト...</td>
                </tr>
                <tr>
                    <td>機能</td>
                    <td>テキストテキストテキスト...</td>
                </tr>
                <tr>
                    <td>機能</td>
                    <td>テキストテキストテキスト...</td>
                </tr>
            </tbody>
        </table>

        <h3 class="text-center mt-5">メインメニュー</h3>
        <div class="row text-center mt-2 mb-4">
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>メッセージ</h5>
                    <i class="fa-regular fa-envelope"></i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>カレンダー</h5>
                    <i class="fa-regular fa-calendar"></i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>買い物</h5>
                    <i class="fa-regular fa-file"></i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>家計簿</h5>
                    <i class="fa-regular fa-pen-to-square"></i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>写真</h5>
                    <i class="fa-regular fa-images"></i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3">
                    <h5>作業リスト</h5>
                    <i class="fa-regular fa-rectangle-list"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
