<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="/styles/style.css">

    <title>500 - Ошибка сервера</title>
</head>
<body class="font-play">

<div id="loader">
    <span class="loading loading-dots loading-xl"></span>
</div>

<div class="container mx-auto px-5 my-5">
    <div class="flex flex-col items-center justify-center min-h-screen pb-30">
        <div class="text-6xl font-bold text-red-500 animate-pulse">
            500
        </div>
        <div class="text-2xl text-gray-600 mb-4">
            Ошибка сервера!
        </div>
        <div class="text-center text-lg text-gray-500">
            Произошла ошибка на сервере. Попробуйте позже или вернитесь на главную страницу.
        </div>
        <a href="{{route('index')}}" class="btn btn-neutral mt-4">Главная страница</a>
    </div>
</div>

{{--jquery--}}
<script src="/plugins/jquery-3.7.1.min.js"></script>

@vite('resources/js/app.js')

</body>

</html>



