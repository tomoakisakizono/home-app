<!-- <header class="mb-5">
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
        <a class="navbar-brand" href="/">ホームコミュニケーション</a>
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#nav-bar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-bar">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a href="" class="nav-link">新規ユーザ登録</a></li>
                <li class="nav-item"><a href="" class="nav-link">ログイン</a></li>
            </ul>
        </div>
    </nav>
</header> -->
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Home Communication</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if(Auth::check())
                <li class="nav-item"><a class="nav-link text-white" href="#">{{ Auth::user()->name }}</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">ログアウト</a></li>
                @else
                <li class="nav-item"><a class="nav-link text-white" href="{{ route('register.form') }}">新規ユーザ登録</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#">ログイン</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
