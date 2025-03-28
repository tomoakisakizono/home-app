<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <div id="top" class="container-fluid">
        <a class="navbar-brand text-white" href="#" data-bs-toggle="modal" data-bs-target="#menuModal">Home Communication</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if(Auth::check())
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('pair.show') }}">{{ Auth::user()->name }}</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('pair.edit') }}">ペア編集</a></li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        ログアウト
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>                
                @else
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('register.form') }}">新規ユーザ登録</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
