@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ファミリーに参加</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('family.join.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="invite_code" class="form-label">招待コード</label>
            <input type="text" name="invite_code" id="invite_code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">参加する</button>
    </form>
</div>
@endsection
