@extends('layouts.app')

@section('content')
<div class="container mb-4">
    <h2>写真一覧</h2>

    {{-- 写真投稿フォーム --}}
    <div class="card mb-4">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('photos.multipleUpload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="images" class="form-label">写真をアップロード（最大10枚）</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple required>
                    <ul id="file-list" class="mt-2"></ul>
                </div>

                <div class="mb-3">
                    <label for="photo_date" class="form-label">撮影日</label>
                    <input type="date" class="form-control" id="photo_date" name="photo_date"
                        value="{{ old('photo_date', today()->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">カテゴリ</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">選択してください</option>
                        <option value="家族">家族</option>
                        <option value="子ども">子ども</option>
                        <option value="メニュー">メニュー</option>
                        <option value="お出かけ">お出かけ</option>
                        <option value="その他">その他</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">コメント（任意）</label>
                    <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">投稿する</button>
            </form>
        </div>
    </div>

    <script>
        let selectedFiles = [];

        function updateFileList(event) {
            let files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                selectedFiles.push(files[i]); // 選択されたファイルを追加
            }

            // フォームの `input type="file"` を更新
            let dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('images').files = dataTransfer.files;

            // 選択したファイルのリストを表示
            displayFileList();
        }

        function displayFileList() {
            let fileList = document.getElementById("file-list");
            fileList.innerHTML = "";
            selectedFiles.forEach((file, index) => {
                let listItem = document.createElement("li");
                listItem.textContent = file.name + " ";
                let removeButton = document.createElement("button");
                removeButton.textContent = "削除";
                removeButton.className = "btn btn-sm btn-danger ms-2";
                removeButton.onclick = function () {
                    removeFile(index);
                };
                listItem.appendChild(removeButton);
                fileList.appendChild(listItem);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1); // 配列から削除

            // フォームの `input type="file"` を更新
            let dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('images').files = dataTransfer.files;

            displayFileList();
        }
    </script>

    {{-- 検索フォーム --}}
    <form method="GET" class="mb-3">
        <input type="text" name="keyword" placeholder="コメント検索" class="form-control" value="{{ request('keyword') }}">
        <div class="d-flex align-items-center gap-2 mt-1">
            <label for="category" class="form-label mb-0">カテゴリ：</label>
            <select name="category" id="category" class="form-select w-auto d-inline-block me-2">
                <option value="">すべて</option>
                <option value="家族" {{ request('category') == '家族' ? 'selected' : '' }}>家族</option>
                <option value="子ども" {{ request('category') == '子ども' ? 'selected' : '' }}>子ども</option>
                <option value="メニュー" {{ request('category') == 'メニュー' ? 'selected' : '' }}>メニュー</option>
                <option value="お出かけ" {{ request('category') == 'お出かけ' ? 'selected' : '' }}>お出かけ</option>
                <option value="その他" {{ request('category') == 'その他' ? 'selected' : '' }}>その他</option>
            </select>
            <button type="submit" class="btn btn-primary h-100">検索</button>
        </div>
    </form>

    {{-- 写真一覧表示 --}}
    <div class="row">
        @foreach ($photos as $photo)
            <div class="col-md-4 mb-3">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-img-container">
                        @if ($photo->images->count() > 0)
                            <img src="{{ asset('storage/' . $photo->images->first()->image_path) }}" 
                                class="card-img-top img-fluid"
                                style="height: 200px; object-fit: cover; width: 100%;">
                        @else
                            <img src="{{ asset('storage/default-placeholder.jpg') }}" 
                                class="card-img-top img-fluid"
                                style="height: 200px; object-fit: cover; width: 100%;">
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="flex-grow-1">{{ $photo->comment }}</p>
                        <p><small>投稿者: {{ $photo->user->name }}</small></p>
                        <p><small>投稿日: {{ $photo->created_at }}</small></p>
                        <p><small>カテゴリ: {{ $photo->category }}</small></p>
                        <a href="{{ route('photos.show', $photo) }}" class="btn btn-info">詳細</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $photos->links() }}
</div>
<div class="d-flex justify-content-center">
    <a href="{{ route('pair.show') }}" class="btn btn-secondary mb-1">ペアページへ</a>
</div>

@endsection
