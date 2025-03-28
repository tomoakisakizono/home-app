@extends('layouts.app')

@section('content')

@auth
@if(auth()->user()->unreadNotifications->count())
    <div class="dropdown text-end mb-2" style="position: relative;">
        <a class="text-dark position-relative" href="#" role="button" id="notificationDropdown"
            data-bs-toggle="dropdown" aria-expanded="false" onclick="markNotificationsAsRead()">
            🔔
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end"
            aria-labelledby="notificationDropdown"
            style="min-width: 280px; max-width: 90vw; word-break: break-word;">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <li class="dropdown-item small">
                    <a href="{{ $notification->data['link'] }}" class="text-decoration-none d-block">
                        {{ $notification->data['message'] }}
                        <br>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </a>
                </li>
            @empty
                <li class="dropdown-item">通知はありません</li>
            @endforelse
        </ul>
    </div>
@endif
@endauth

@if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

<h3 class="text-center mt-3">メインメニュー</h3>
<div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-3 text-center mt-2 mb-5">
    <div class="col">
        <a href="{{ route('messages.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">メッセージ</h6>
                <i class="fa-regular fa-envelope fa-2x"></i>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('calendar.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">カレンダー</h6>
                <i class="fa-regular fa-calendar fa-2x"></i>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('shopping.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">買い物</h6>
                <i class="fa-regular fa-file fa-2x"></i>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('photos.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">写真</h6>
                <i class="fa-regular fa-images fa-2x"></i>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('videos.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">動画</h6>
                <i class="fa-regular fa-pen-to-square fa-2x"></i>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('tasks.index') }}" class="text-decoration-none">
            <div class="card p-3">
                <h6 class="menu-label text-nowrap text-truncate">作業リスト</h6>
                <i class="fa-regular fa-rectangle-list fa-2x"></i>
            </div>
        </a>
    </div>
</div>

<form action="{{ route('functions.store') }}" method="POST" class="mt-3">
    @csrf
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- 機能選択ボタン -->
        <div class="flex-shrink-0">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="functionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    機能を選択
                </button>
                <ul class="dropdown-menu" aria-labelledby="functionDropdown">
                    <li><a class="dropdown-item function-option" href="#" data-value="メッセージ">メッセージ</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="カレンダー">カレンダー</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="買い物リスト">買い物リスト</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="写真">写真</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="動画">動画</a></li>
                    <li><a class="dropdown-item function-option" href="#" data-value="作業リスト">作業リスト</a></li>
                </ul>
            </div>
            <input type="hidden" name="function_name" id="selectedFunction" value="">
        </div>

        <!-- 詳細入力欄 -->
        <div class="flex-grow-1">
            <textarea class="form-control w-100" name="details" rows="1" placeholder="詳細を入力" required></textarea>
        </div>
    </div>

    <!-- 登録ボタン：常に下に表示 -->
    <div class="text-start mt-2">
        <button type="submit" class="btn btn-primary px-4 w-100">登録</button>
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
        @foreach ($latestFunctions as $function)
            <tr>
                <td>{{ $function->function_name }}</td>
                <td class="text-nowrap">{{ $function->created_at->format('n/j H:i') }}</td>
                <td>{{ $function->details }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="row justify-content-center align-items-center mt-5 mb-3">
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

    function markNotificationsAsRead() {
        fetch("{{ route('notifications.read') }}")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    document.getElementById('notification-badge')?.remove();
                }
            });
    }
</script>

@endsection
