@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container py-4">

    {{-- ✅ ファミリーネームの表示 + 通知ベル --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <h4 class="mb-0">ようこそ、{{ optional(auth()->user()->family)->name }} さん</h4>

        {{-- 🔔 通知ベル（ドロップダウン） --}}
        <div class="dropdown">
            <button class="btn btn-light border position-relative" id="notifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="通知">
                <i class="bi bi-bell"></i>
                <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($unreadCount ?? 0) > 0 ? '' : 'd-none' }}">
                    {{ $unreadCount ?? 0 }}
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0 shadow-sm" aria-labelledby="notifDropdownBtn" style="min-width: 340px;">
                <div class="p-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong>最近の通知</strong>
                        <form id="notifMarkAllForm" method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">すべて既読</button>
                        </form>
                    </div>
                </div>

                {{-- 最近の通知リスト（サーバーで渡せない場合は空でOK） --}}
                <div id="notifList" class="list-group list-group-flush">
                    @isset($recentNotifications)
                        @forelse($recentNotifications->take(5) as $n)
                            @php $data = $n->data; @endphp
                            <a href="{{ $data['url'] ?? route('notifications.index') }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between">
                                <div class="me-3">
                                    <div class="fw-semibold">{{ $data['title'] ?? 'お知らせ' }}</div>
                                    <div class="small text-muted">{{ Str::limit($data['message'] ?? '', 60) }}</div>
                                    <div class="small text-secondary">
                                        {{ \Carbon\Carbon::parse($n->created_at)->locale('ja')->isoFormat('YYYY年M月D日(ddd) HH:mm') }}
                                    </div>
                                </div>
                                @if(is_null($n->read_at))
                                    <span class="badge bg-primary align-self-start">未読</span>
                                @endif
                            </a>
                        @empty
                            <div class="list-group-item text-muted small">通知はありません。</div>
                        @endforelse
                    @else
                        {{-- recentNotifications を渡していない場合のプレースホルダ --}}
                        <div class="list-group-item text-muted small">最新の通知を読み込みました。</div>
                    @endisset
                </div>

                <div class="p-2 border-top text-end">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link">すべて表示</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ ファミリーメンバーのアイコン表示 --}}
    <div class="d-flex gap-2 mb-3">
        @foreach (optional(auth()->user()->family)->users ?? [] as $member)
            <img src="{{ $member->profile_image ? asset('storage/' . $member->profile_image) : 'https://placehold.co/80x80?text=%20' }}"
                 alt="{{ $member->name }}"
                 class="rounded-circle border" width="40" height="40">
        @endforeach
    </div>

    {{-- 🔔 未読メッセージ通知（カード） --}}
    @if(($unreadCount ?? 0) > 0)
        <div id="unreadMessageAlert" class="alert alert-warning py-2 px-3 mb-3">
            <i class="bi bi-bell-fill me-2"></i>
            <span id="unreadMessageText">{{ $unreadCount }}件の未読メッセージがあります。</span>
        </div>
    @endif

    {{-- 📅 今日の予定 --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5><i class="bi bi-calendar-event me-2"></i>今日の予定</h5>
            @if(!empty($todayEvent))
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
            @if(!empty($latestMessage))
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
                        @if(($item['badge'] ?? 0) > 0)
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

{{-- ===== 通知のJS（未読数ポーリング + すべて既読の即時反映） ===== --}}
<script>
(function(){
    const badge = document.getElementById('notif-badge');
    const alertBox = document.getElementById('unreadMessageAlert');
    const alertText = document.getElementById('unreadMessageText');

    async function refreshUnreadCount() {
        try {
            const res = await fetch('{{ route('notifications.unread-count') }}', { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            const count = Number(data.count || 0);

            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
            if (alertBox && alertText) {
                if (count > 0) {
                    alertText.textContent = `${count}件の未読メッセージがあります。`;
                    alertBox.classList.remove('d-none');
                } else {
                    alertBox.classList.add('d-none');
                }
            }
        } catch (e) { /* noop */ }
    }

    // すべて既読（ページ遷移なしで即時反映）
    const markAllForm = document.getElementById('notifMarkAllForm');
    if (markAllForm) {
        markAllForm.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            try {
                const formData = new FormData(markAllForm);
                const res = await fetch(markAllForm.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    // バッジとアラートをゼロ化
                    if (badge) badge.classList.add('d-none');
                    if (alertBox) alertBox.classList.add('d-none');
                    // ドロップダウン内の未読バッジを消す
                    document.querySelectorAll('#notifList .badge.bg-primary').forEach(el => el.remove());
                }
            } catch (e) { /* noop */ }
        });
    }

    // 初回 & ポーリング
    refreshUnreadCount();
    setInterval(refreshUnreadCount, 20000); // 20秒ごと
})();
</script>
@endsection
