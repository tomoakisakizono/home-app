@extends('layouts.app')

@section('content')
    <h2>ペア設定</h2>

    <!-- 成功メッセージ（招待コードを含む） -->
    @include('partials.alerts')
    
    <!-- 既存の招待コードがある場合 -->
    @if(isset($pair) && $pair->invite_code)
        <h3>発行済みの招待コード</h3>
        <p><strong>{{ $pair->invite_code }}</strong></p>
    @endif

    <!-- 招待コードを発行 -->
    <form action="{{ route('pair.invite') }}" method="POST">
        @csrf
        <label>相手のメールアドレス:</label>
        <input type="email" name="email" required>
        <button type="submit">招待を送信</button>
    </form>

    <!-- 招待コードでペアに参加 -->
    <form action="{{ route('pair.accept') }}" method="POST">
        @csrf
        <label>招待コード:</label>
        <input type="text" name="invite_code" required>
        <button type="submit">ペアに参加</button>
    </form>
@endsection
