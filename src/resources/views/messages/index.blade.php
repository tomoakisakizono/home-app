@extends('layouts.app')

@section('content')
<style>
    .chat-box {
        background-color: #ffffff;
        color: #212529;
        padding: 16px;
        border-radius: 12px;
        height: 65vh;
        overflow-y: auto;
        border: 1px solid #dee2e6;
    }

    .message-left, .message-right {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 18px;
        margin-bottom: 12px;
        display: inline-block;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .message-left {
        background-color: #f1f3f5;
        color: #212529;
        border-bottom-left-radius: 4px;
        margin-right: auto;
    }

    .message-right {
        background-color: #d0ebff;
        color: #0c5460;
        border-bottom-right-radius: 4px;
        margin-left: auto;
    }

    .message-meta {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 4px;
        text-align: right;
    }

    .chat-input {
        background-color: #fff;
        color: #212529;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    .chat-input:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .send-btn {
        border-radius: 8px;
        margin-left: 10px;
        padding: 0.75rem 1.25rem;
    }
</style>

<div class="container py-4">
    <div class="d-flex overflow-auto px-3 py-2 border-bottom gap-3 align-items-center">
        {{-- 家族全体 --}}
        @include('messages.family_nav')

        {{-- 各メンバー --}}
        @include('messages.member_nav')
    </div>

    <h4 class="mt-2">送信先：{{ $chatPartner->name ?? '家族全体' }}</h4>

    @include('partials.alerts')

    <div id="chat-scroll" class="chat-box d-flex flex-column mb-3 shadow-sm">
        @php
            $previousDate = null;
        @endphp

        @forelse ($messages as $message)
            @php
                $messageDate = $message->created_at->format('Y年n月j日（D）');
            @endphp

            {{-- 日付が変わったら表示 --}}
            @if ($previousDate !== $messageDate)
                <div class="text-center my-2 text-muted small">
                    {{ $messageDate }}
                </div>
                @php $previousDate = $messageDate; @endphp
            @endif

            <div class="@if ($message->sender_id === auth()->id()) message-right @else message-left @endif">
                {{ $message->content }}
                <div class="message-meta">
                    @if ($message->sender_id === auth()->id())
                        {{ $message->created_at->format('H:i') }}
                    @else
                        {{ $message->sender->name ?? '匿名' }}・{{ $message->created_at->format('H:i') }}
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted">まだメッセージがありません。</p>
        @endforelse
    </div>

    <form action="{{ route('messages.store') }}" method="POST" class="d-flex align-items-center">
        @csrf

        @if (isset($chatPartner))
            <input type="hidden" name="receiver_id" value="{{ $chatPartner->id }}">
        @endif
        
        <input type="text" name="content" placeholder="メッセージを入力..." class="form-control chat-input" required>
        <button type="submit" class="btn btn-primary rounded-circle px-3 send-btn"> <i class="bi bi-send-fill"></i></button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ戻る</a>
    </div>
</div>

<script>
    // 自動スクロール
    const chat = document.getElementById('chat-scroll');
    if (chat) {
        chat.scrollTop = chat.scrollHeight;
    }
</script>
@endsection
