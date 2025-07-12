<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Кабинет преподавателя')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('teacher.nav')
    <div class="main-content">
        @yield('content')
    </div>
</body>
</html> 