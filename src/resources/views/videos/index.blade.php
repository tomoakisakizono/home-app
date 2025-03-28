@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>動画一覧・投稿</h2>

    {{-- 投稿成功メッセージ --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 投稿フォーム --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('videos.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="youtube_url" class="form-label">YouTubeのURL</label>
                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" required>
                </div>

                <div class="mb-3">
                    <label for="registered_at" class="form-label">登録日</label>
                    <input type="date" class="form-control" id="registered_at" name="registered_at"
                        value="{{ old('registered_at', today()->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">カテゴリ</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">選択してください</option>
                        <option value="子ども">子ども</option>
                        <option value="外食用">外食用</option>
                        <option value="お気に入り">お気に入り</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">コメント（任意）</label>
                    <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">動画を投稿</button>
            </form>
        </div>
    </div>

    {{-- カテゴリ検索フォーム --}}
    <form method="GET" class="mb-3">
        <div class="d-flex align-items-center gap-2">
            <label for="category" class="form-label mb-0">カテゴリ：</label>
            <select name="category" id="category" class="form-select w-auto d-inline-block me-2">
                <option value="">すべて</option>
                <option value="子ども" {{ request('category') == '子ども' ? 'selected' : '' }}>子ども</option>
                <option value="外食用" {{ request('category') == '外食用' ? 'selected' : '' }}>外食用</option>
                <option value="お気に入り" {{ request('category') == 'お気に入り' ? 'selected' : '' }}>お気に入り</option>
            </select>
            <button type="submit" class="btn btn-primary h-100">検索</button>
        </div>
    </form>

    {{-- 動画一覧 --}}
    <div class="row">
        @foreach ($videos as $video)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        {{-- YouTube埋め込み表示 --}}
                        @php
                            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([a-zA-Z0-9_-]+)/', $video->youtube_url, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp

                        @if ($videoId)
                            <div class="ratio ratio-16x9 mb-2">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $videoId }}"
                                    frameborder="0"
                                    allowfullscreen
                                    class="w-100"
                                    style="border: 0;">
                                </iframe>
                            </div>
                        @endif

                        <p>{!! $video->comment !== null ? e($video->comment) : '&nbsp;' !!}</p>
                        <p><small>登録日: {{ $video->registered_at }}</small></p>
                        <p><small>カテゴリ: {{ $video->category ?? '' }}</small></p>

                        {{-- 編集・削除ボタン --}}
                        @if (Auth::id() === $video->user_id)
                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('videos.edit', $video) }}" class="btn btn-warning btn-sm">編集</a>
                            <form action="{{ route('videos.destroy', $video) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">削除</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
{{ $videos->withQueryString()->links() }}
<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-3">ペアページへ</a>
</div>
@endsection
