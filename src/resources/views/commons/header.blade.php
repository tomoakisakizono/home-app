<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Home Communication</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if(Auth::check())
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('pair.show') }}">{{ Auth::user()->name }}</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('pair.edit') }}">ペアページ</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('logout') }}">ログアウト</a></li>
                @else
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('register.form') }}">新規ユーザ登録</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
