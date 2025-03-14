<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home Communication')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 20px; }
        footer { background: #0d6efd; color: white; padding: 10px; text-align: center; }
    </style>
</head>
<body>

    @include('commons.header')

    <div class="container">
        @yield('content')
    </div>

    @include('commons.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/cf5eb7ebd9.js" crossorigin="anonymous"></script>
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
</body>
</html>
