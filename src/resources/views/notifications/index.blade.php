@extends('layouts.app')

@section('title', '通知')

@section('content')

<div class="container py-3">
  <h4 class="mb-3">通知</h4>

  <div class="list-group">
    @forelse ($notifications as $n)
      @php $data = $n->data; @endphp
      <a href="{{ $data['url'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
        <div class="me-2">
          <div class="fw-bold">{{ $data['title'] ?? 'お知らせ' }}</div>
          <div>{{ $data['message'] ?? '' }}</div>
          <small class="text-muted">
            {{ \Carbon\Carbon::parse($n->created_at)->locale('ja')->isoFormat('YYYY年M月D日(ddd) HH:mm') }}
          </small>
        </div>
        @if(is_null($n->read_at))
          <span class="badge bg-primary rounded-pill">未読</span>
        @endif
      </a>
    @empty
      <div class="text-muted">通知はありません。</div>
    @endforelse
  </div>
</div>
@endsection
