@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="text-center my-4">ペア設定</h4>
            @include('partials.alerts')

            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="mb-4 text-muted">
                        相手のメールアドレスに招待を送信するか、<br>
                        相手から受け取った招待コードを入力してペアを作成します。
                    </p>

                    @if(isset($pair) && $pair->invite_code)
                        <div class="mb-4">
                            <h6>発行済みの招待コード</h6>
                            <p class="h5 font-monospace text-primary">{{ $pair->invite_code }}</p>
                        </div>
                    @endif

                    <hr>

                    <form action="{{ route('pair.invite') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">相手のメールアドレス</label>
                            <input type="email" class="form-control" name="email" required placeholder="example@example.com">
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">招待を送信</button>
                    </form>

                    <form action="{{ route('pair.accept') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="invite_code" class="form-label">招待コード</label>
                            <input type="text" class="form-control" name="invite_code" required placeholder="A1E72DEB">
                        </div>
                        <button type="submit" class="btn btn-success w-100">ペアに参加</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
