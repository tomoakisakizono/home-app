{{-- 各メンバー --}}
@foreach ($members as $member)
    <div class="text-center">
        <a href="{{ route('messages.user', $member->id) }}" class="text-decoration-none text-dark">
            <div class="ratio ratio-1x1 rounded-circle overflow-hidden" style="width: 56px; background-color: #f1f3f5;">
                <img src="{{ asset('storage/' . $member->profile_image) }}"
                    alt="icon"
                    onerror="this.src='{{ asset('images/default_user.png') }}'"
                    class="img-fluid w-100 h-100 object-fit-cover">
            </div>
            <div class="small text-nowrap mt-1">{{ $member->name }}</div>
        </a>
    </div>
@endforeach
