@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>写真の編集</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 現在の画像一覧と削除ボタン --}}
    @if ($photo->images->count() > 0)
        <div class="mb-3">
            <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($photo->images as $index => $image)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" style="max-height: 500px; object-fit: contain;">
                            <div class="carousel-caption">
                                <form action="{{ route('photos.deleteImage', ['photo' => $photo->id, 'photoImage' => $image->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('この画像を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                </form>
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
        </div>
    @else
        <p class="mt-4">この投稿には写真がありません。</p>
    @endif

    <form action="{{ route('photos.update', $photo) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="photo_date" class="form-label">撮影日</label>
            <input type="date" class="form-control" id="photo_date" name="photo_date" value="{{ old('photo_date', $photo->photo_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">カテゴリ</label>
            <div class="d-flex w-100 flex-wrap justify-content-between">
                <input type="radio" class="btn-check" name="category" id="category_family" value="家族"
                    {{ old('category', $photo->category) == '家族' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary category-btn" for="category_family">家族</label>

                <input type="radio" class="btn-check" name="category" id="category_kids" value="子ども"
                    {{ old('category', $photo->category) == '子ども' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary category-btn" for="category_kids">子ども</label>

                <input type="radio" class="btn-check" name="category" id="category_menu" value="メニュー"
                    {{ old('category', $photo->category) == 'メニュー' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary category-btn" for="category_menu">メニュー</label>

                <input type="radio" class="btn-check" name="category" id="category_outing" value="お出かけ"
                    {{ old('category', $photo->category) == 'お出かけ' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary category-btn" for="category_outing">お出かけ</label>

                <input type="radio" class="btn-check" name="category" id="category_other" value="その他"
                    {{ old('category', $photo->category) == 'その他' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary category-btn" for="category_other">その他</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">コメント</label>
            <textarea class="form-control" id="comment" name="comment" rows="3">{{ old('comment', $photo->comment) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">更新する</button>
        <a href="{{ route('photos.show', $photo) }}" class="btn btn-secondary">キャンセル</a>
    </form>
</div>
@endsection
