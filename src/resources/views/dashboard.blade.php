@extends('layouts.app')

@section('content')
<div class="container py-4">

{{-- ✅ ファミリーネームの表示 --}}
    <h4>ようこそ、{{ auth()->user()->family->name }} さん</h4>
        {{-- ✅ ファミリーメンバーのアイコン表示 --}}
    <div class="d-flex gap-2 mb-3">
        @foreach (auth()->user()->family->users as $member)
            <img src="{{ asset('storage/' . $member->profile_image) }}"
                alt="{{ $member->name }}"
                class="rounded-circle border"
                width="40" height="40">
        @endforeach
    </div>

    {{-- 🔔 未読メッセージ通知 --}}
    @if($unreadCount > 0)
        <div class="alert alert-warning py-2 px-3 mb-3">
            <i class="bi bi-bell-fill me-2"></i>
            {{ $unreadCount }}件の未読メッセージがあります。
        </div>
    @endif

    {{-- 📅 今日の予定 --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5><i class="bi bi-calendar-event me-2"></i>今日の予定</h5>
            @if($todayEvent)
                <p>
                    {{ \Carbon\Carbon::parse($todayEvent->event_time)->format('H:i') }}
                    {{ $todayEvent->title }}
                </p>
            @else
                <p class="text-muted">本日の予定はありません。</p>
            @endif
        </div>
    </div>

    {{-- 💬 最新メッセージ --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5><i class="bi bi-chat-dots me-2"></i>最新メッセージ</h5>
            @if($latestMessage)
                <p class="mb-0">「{{ Str::limit($latestMessage->content, 50) }}」<br>
                    from {{ $latestMessage->sender->name }}</p>
            @else
                <p class="text-muted">新着メッセージはありません。</p>
            @endif
        </div>
    </div>

    {{-- 🧭 機能メニュー --}}
    <div class="row text-center mb-4">
        @foreach($menuItems as $item)
            <div class="col-4 col-md-2 mb-3">
                <a href="{{ route($item['route']) }}" class="text-decoration-none text-dark">
                    <div class="position-relative">
                        <i class="bi {{ $item['icon'] }} fs-2"></i>
                        @if($item['badge'] > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </div>
                    <div class="mt-1">{{ $item['label'] }}</div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- 🧡 感謝ログ表示 --}}
    <div class="text-center fs-5">
        <span class="text-danger">❤️</span>
        今日の「ありがとう」は <strong>{{ $gratitudeCount }}</strong> 件です。
    </div>

</div>
@endsection
