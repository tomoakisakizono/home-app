<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home Communication')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons ← 必須！ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

    <!-- カスタムCSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        body { background-color: #f8f9fa; }
        footer { background: #0d6efd; color: white; padding: 10px; text-align: center; }
        .container { padding-bottom: 50px; }
    </style>
    <style>
    /* 今日の予定：時間を固定幅、タイトルを可変にして折り返しを最小化 */
    .today-event{display:flex;align-items:center;gap:.5rem}
    .today-event-time{flex:0 0 56px;font-weight:700;color:#0d6efd}
    .today-event-title{flex:1;word-break:break-word}

    /* 通知：行間を詰めて読みやすく */
    .notice-list{list-style:none;padding-left:0;margin:0}
    .notice-list li{margin: .25rem 0;line-height:1.35}

    /* 最近の写真投稿：正方形サムネ */
    .photo-thumb{width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:.6rem;border:1px solid rgba(0,0,0,.08)}
    /* カルーセル矢印のタップ領域を少し大きく */
    .carousel-control-prev, .carousel-control-next{width:12%}

    /* 最近のメッセージ：モバイルでの視認性 */
    .recent-message{font-size:.95rem;line-height:1.35;margin-bottom:.35rem}
    
    /* 通知テーブル（PCは2列、スマホは縦積み） */
    .table-notice td:first-child{width:6rem; white-space:nowrap; font-weight:700;}
    .table-notice .notice-title{font-weight:600;}
    .table-notice .notice-body{font-size:.95rem; line-height:1.4;}

    /* iPhone SE 等の幅狭対策 */
    @media (max-width: 360px){
    .today-event-time{flex-basis:52px}    
    .card .display-6{font-size:1.7rem} /* Featureカードの絵文字やアイコンが大き過ぎる場合の抑制 */
    }

    @media (max-width: 480px){
    .table-notice tr{border-top:1px solid #dee2e6;}
    .table-notice td{display:block; width:100%!important; border-top:0;}
    .table-notice td:first-child{margin:.25rem 0 .1rem;}
    }
    </style>
</head>
<body>

    @include('commons.header')

    <div class="container">
        @yield('content')
    </div>

    @include('commons.footer')

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <!-- フラッシュメッセージ自動削除 -->
    <script>
        setTimeout(function() {
            let alertBox = document.getElementById('success-alert');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 20000);
    </script>
    
    <script src="{{ asset('js/notifications.js') }}"></script>

</body>
</html>
