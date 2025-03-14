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

<form action="{{ route('pair.functions.store') }}" method="POST" class="mt-3">
    @csrf
    <div class="d-flex align-items-center">
        <!-- 🔹 プルダウンボタン -->
        <div class="me-2">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="functionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    機能を選択
                </button>
                <ul class="dropdown-menu" aria-labelledby="functionDropdown">
                    <li><a class="dropdown-item function-option" href="#" data-value="メッセージ">メッセージ</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="カレンダー">カレンダー</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="買い物リスト">買い物リスト</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="家計簿">家計簿</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="写真">写真</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="作業リスト">作業リスト</a></li>
                </ul>
            </div>
            <input type="hidden" name="function_name" id="selectedFunction" value="">
        </div>

        <!-- 🔹 詳細入力エリア -->
        <div class="me-2" style="width: 800px;">
            <textarea class="form-control" name="details" rows="1" placeholder="詳細を入力" required></textarea>
        </div>
        <!-- 🔹 送信ボタン -->
        <div class="ms-2">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
    </div>
</form>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th style="width: 20%;">機能</th>
            <th style="width: 30%;">日付</th>
            <th style="width: 50%;" class="text-start">詳細</th>
        </tr>
    </thead>
    <tbody>
        @foreach($latestFunctions as $function)
        <tr>
            <td>{{ $function->function_name }}</td>
            <td>{{ $function->created_at->format('Y-m-d H:i') }}</td> <!-- 🔹 日付カラム -->
            <td class="text-start">{{ $function->details }}</td>
        </tr>
        @endforeach
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const functionDropdown = document.getElementById("functionDropdown");
        const selectedFunctionInput = document.getElementById("selectedFunction");

        document.querySelectorAll(".function-option").forEach(item => {
            item.addEventListener("click", function (e) {
                e.preventDefault();
                const selectedValue = this.getAttribute("data-value");
                functionDropdown.textContent = selectedValue; // ボタンのテキストを変更
                selectedFunctionInput.value = selectedValue; // フォームの隠しフィールドにセット
            });
        });
    });
</script>

@endsection
