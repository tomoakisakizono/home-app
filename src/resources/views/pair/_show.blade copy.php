@extends('layouts.app')

@section('content')
    <h2>ペア情報</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($pair)
        <p><strong>ペアID:</strong> {{ $pair->id }}</p>
        <p><strong>招待コード:</strong> {{ $pair->invite_code }}</p>
        <p><strong>ステータス:</strong> {{ $pair->status }}</p>
        <p><strong>ユーザー1:</strong> {{ $pair->user1->name }} ({{ $pair->user1->email }})</p>
        <p><strong>ユーザー2:</strong> {{ $pair->user2->name ?? '未設定' }}</p>
    @else
        <p>ペアが設定されていません</p>
        <a href="{{ route('pair.setup') }}" class="btn btn-primary">ペアを設定する</a>
    @endif
@endsection
