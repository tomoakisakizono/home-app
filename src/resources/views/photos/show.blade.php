@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>写真の詳細</h2>
    {{-- スライドショー（投稿の画像を切り替え） --}}
    @if($photo->images->count() > 0)
        <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($photo->images as $index => $image)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" style="max-height: 500px; object-fit: contain;">
                        <div class="carousel-caption">
                            <a href="{{ route('photos.download', $image) }}" class="btn btn-success btn-sm">ダウンロード</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    @else
        <p>この投稿には写真がありません。</p>
    @endif

    <div class="card mt-3">
        <!-- <img src="{{ asset('storage/' . $photo->image_path) }}" class="card-img-top" style="max-height: 500px; object-fit: contain;"> -->
        <div class="card-body">
            <p><strong>コメント:</strong> {{ $photo->comment }}</p>
            <p><strong>撮影日:</strong> {{ $photo->photo_date }}</p>
            <p><strong>カテゴリ:</strong> {{ $photo->category }}</p>

            {{-- 投稿者のみ削除ボタンを表示 --}}
            @if (Auth::id() === $photo->user_id)
                <div class="d-flex flex-wrap gap-1 mb-2">
                    <a href="{{ route('photos.edit', $photo) }}" class="btn btn-warning">編集</a>
                    <form action="{{ route('photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">削除</button>
                    </form>
                </div>
            @endif

            {{-- 一括ダウンロード・一覧へ戻るボタン --}}
            <div class="d-flex flex-wrap gap-1 mb-2">
                <a href="{{ route('photos.downloadAll', $photo) }}" class="btn btn-success">一括ダウンロード</a>
                <a href="{{ route('photos.index') }}" class="btn btn-secondary">一覧に戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
