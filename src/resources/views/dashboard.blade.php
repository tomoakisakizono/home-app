@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4" style="max-width:980px;">

    {{-- ヘッダ：アバター + 家族名 + 通知ベル（明るいトーン） --}}
    <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-4 shadow-sm"
         style="background:linear-gradient(90deg,#f8f9fa,#ffffff);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-light border me-3" style="width:48px;height:48px;"></div>
            <h3 class="m-0 text-dark">{{ $family->name ?? 'Your Family' }}</h3>
        </div>

        {{-- 通知ページ自体はdashboardでは非表示要件の“お知らせ一覧”に該当するため、リンクは残しつつボタンは控えめに --}}
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary position-relative" aria-label="Notifications">
            <i class="bi bi-bell"></i>
            @if(($unreadNotificationCount ?? 0) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadNotificationCount }}
                </span>
            @endif
        </a>
    </div>

    {{-- 家族名 / メンバー --}}
    <h4 class="mb-0 text-dark">ようこそ、{{ $family->name ?? 'ゲスト' }} さん</h4>

    <div class="d-flex gap-2 mb-3">
        @php $members = $familyMembers ?? collect(); @endphp
        @forelse($members as $member)
            <img
                src="{{ $member->profile_image ? asset('storage/'.$member->profile_image) : 'https://placehold.co/80x80?text=%20' }}"
                alt="{{ $member->name }}"
                class="rounded-circle border"
                width="40" height="40" loading="lazy">
        @empty
            <span class="text-muted">メンバー情報がありません。</span>
        @endforelse
    </div>

    {{-- 今日の予定（旧: Today’s Schedule） --}}
    <section class="mb-4">
        <h4 class="fw-bold mb-3 text-dark">今日の予定</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if(!empty($todayEvent))
                    <div class="d-flex align-items-start">
                        <div class="fw-bold me-3 text-primary" style="min-width:72px;">
                            {{ \Carbon\Carbon::parse($todayEvent->start_at ?? $todayEvent->event_time)->format('H:i') }}
                        </div>
                        <div class="fs-5 text-dark">
                            {{ $todayEvent->title }}
                            @if(!empty($todayEvent->memo))
                                <span class="text-muted">（{{ $todayEvent->memo }}）</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-muted">本日の予定はありません。</div>
                @endif
            </div>
        </div>
    </section>

    {{-- 通知（旧: Notifications）※ダッシュボードに最新2件だけ軽く表示 --}}
    <section class="mb-4">
        @php $notifs = ($notifications ?? collect()); @endphp
        <h4 class="fw-bold mb-3 text-dark">通知</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @forelse($notifs->take(2) as $n)
                    @php $data = is_array($n->data ?? null) ? $n->data : []; @endphp
                    <div class="mb-2">
                        <i class="bi bi-dot"></i>
                        <span class="text-dark">{{ $data['title'] ?? $n->title ?? 'お知らせ' }}</span>
                        <span class="text-muted">{{ isset($data['message']) ? '：'.$data['message'] : '' }}</span>
                    </div>
                @empty
                    <div class="text-muted">新しい通知はありません。</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- 最近の写真投稿（旧: Photos） --}}
    <section class="mb-4">
        <h4 class="fw-bold mb-3 text-dark">最近の写真投稿</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @php $photoList = $photos ?? collect(); @endphp
                @forelse($photoList as $photo)
                    @php
                        $path   = $photo->path ?? '';
                        $exists = $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
                        $src    = $exists ? \Illuminate\Support\Facades\Storage::url($path) : asset('images/placeholder.png');
                    @endphp
                    <img src="{{ $src }}" alt="{{ $photo->title ?? 'photo' }}"
                         width="80" height="80" class="me-2 mb-2 rounded border"
                         style="object-fit:cover" loading="lazy">
                @empty
                    <div class="text-muted">写真はまだありません。</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- 最近のメッセージ（旧: Recent Messages） --}}
    <section class="mb-5">
        <h4 class="fw-bold mb-3 text-dark">最近のメッセージ</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @php $msgList = $messages ?? collect(); @endphp
                @forelse($msgList->take(2) as $m)
                    <div class="fs-6 mb-2">・{{ \Illuminate\Support\Str::limit($m->content, 60) }}</div>
                @empty
                    <div class="text-muted">メッセージはまだありません。</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- メイン機能（旧: Feature Hub） --}}
    @php
        $unreadMsg  = $unreadMessageCount ?? 0;

        // 表示するカードのみ（ご依頼の非表示項目は含めない）
        // 非表示一覧：
        //  お知らせ一覧 / 予定を追加 / タスクを追加 / カテゴリ追加 /
        //  ファミリー情報 / 招待コード/送信 / 招待コードで参加 /
        //  管理者:メンバー作成 / プロフィール編集 / 旧ペア機能
        $features = [
            ['label' => 'Family Chat',   'desc' => '家族チャット',   'icon' => 'bi-chat-dots',     'route' => route('messages.family'), 'badge' => $unreadMsg],
            ['label' => 'Calendar',      'desc' => '予定一覧',       'icon' => 'bi-calendar-event','route' => route('calendar.index')],
            ['label' => 'Tasks',         'desc' => 'タスク一覧',     'icon' => 'bi-check-square',  'route' => route('tasks.index')],
            ['label' => 'Shopping List', 'desc' => '買い物リスト',   'icon' => 'bi-cart',          'route' => route('shopping.index')],
            ['label' => 'Photos',        'desc' => '写真一覧・投稿', 'icon' => 'bi-image',         'route' => route('photos.index')],
            ['label' => 'Videos',        'desc' => '動画一覧・投稿', 'icon' => 'bi-camera-video',  'route' => route('videos.index')],
        ];
    @endphp

    {{-- メイン機能（横スクロール） --}}
    <section class="mb-4">
        <h4 class="fw-bold mb-3">メイン機能</h4>
        <div class="d-flex flex-nowrap overflow-auto gap-3 pb-2">
            {{-- 1カードあたり最小幅を指定してスライド可能に --}}
            <a href="{{ route('messages.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">💬</div>
                <div class="fw-bold mt-2">Family Chat</div>
                <div class="text-muted small">家族チャット</div>
                </div>
            </div>
            </a>

            <a href="{{ route('calendar.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">📅</div>
                <div class="fw-bold mt-2">Calendar</div>
                <div class="text-muted small">予定一覧</div>
                </div>
            </div>
            </a>

            <a href="{{ route('tasks.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">✅</div>
                <div class="fw-bold mt-2">Tasks</div>
                <div class="text-muted small">タスク一覧</div>
                </div>
            </div>
            </a>

            <a href="{{ route('shopping.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">🛒</div>
                <div class="fw-bold mt-2">Shopping List</div>
                <div class="text-muted small">買い物リスト</div>
                </div>
            </div>
            </a>

            <a href="{{ route('photos.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">🖼️</div>
                <div class="fw-bold mt-2">Photos</div>
                <div class="text-muted small">写真一覧・投稿</div>
                </div>
            </div>
            </a>

            <a href="{{ route('videos.index') }}" class="text-decoration-none">
            <div class="card text-center shadow-sm feature-card-minw">
                <div class="card-body">
                <div class="display-6">🎬</div>
                <div class="fw-bold mt-2">Videos</div>
                <div class="text-muted small">動画一覧・投稿</div>
                </div>
            </div>
            </a>
        </div>

        {{-- 追加アクション（任意）：ログアウト --}}
        <div class="text-end mt-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </section>

</div>
@endsection
