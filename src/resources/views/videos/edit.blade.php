@extends('layouts.app')

@section('content')

<div class="container mb-4">
    <h2>動画編集</h2>
    @include('partials.alerts')

    <form action="{{ route('videos.update', $video) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="youtube_url" class="form-label">YouTubeのURL</label>
            <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                value="{{ old('youtube_url', $video->youtube_url) }}" required>
        </div>

        <div class="mb-3">
            <label for="registered_at" class="form-label">登録日</label>
            <input type="date" class="form-control" id="registered_at" name="registered_at"
                value="{{ old('registered_at', $video->registered_at) }}" required>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">カテゴリ</label>
            <select class="form-control" id="category" name="category" required>
                <option value="子ども" {{ old('category', $video->category) == '子ども' ? 'selected' : '' }}>子ども</option>
                <option value="外食用" {{ old('category', $video->category) == '外食用' ? 'selected' : '' }}>外食用</option>
                <option value="お気に入り" {{ old('category', $video->category) == 'お気に入り' ? 'selected' : '' }}>お気に入り</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">コメント（任意）</label>
            <textarea class="form-control" id="comment" name="comment" rows="2">{{ old('comment', $video->comment) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">更新する</button>
        <a href="{{ route('videos.index') }}" class="btn btn-secondary">戻る</a>
    </form>
</div>
@endsection
