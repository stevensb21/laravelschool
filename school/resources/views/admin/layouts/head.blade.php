<?php
    use Illuminate\Http\Request;

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет администратора</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/nav.css'])
    <link rel="stylesheet" href="{{ asset('css/admin/account.css') }}">
    

    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+Pro:wght@400;700&display=swap" rel="stylesheet">
    @yield('head')
</head>
<body>

    @yield('content')

</body>
</html> 