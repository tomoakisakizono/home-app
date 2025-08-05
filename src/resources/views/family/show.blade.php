@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">ファミリー設定</h2>

    @include('partials.alerts')

    <div class="card p-3 shadow-sm mb-4">
        <h5 class="mb-3">ファミリー名：{{ $family->name ?? '未設定' }}</h5>

        {{-- 招待コード表示 --}}
        @if ($family->invite_code)
            <div class="alert alert-info">
                招待コード：<strong>{{ $family->invite_code }}</strong>
            </div>
        @else
            <form action="{{ route('family.invite') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">招待コードを発行する</button>
            </form>
        @endif
    </div>

    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">ダッシュボードに戻る</a>
    </div>
</div>
@endsection
