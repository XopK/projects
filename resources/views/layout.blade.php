<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('meta')

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="/styles/style.css">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css"/>

    @yield('style')

    <title>@yield('title', 'Все танцы')</title>
</head>
<body class="font-play">

<div id="loader">
    <span class="loading loading-dots loading-xl"></span>
</div>


<div class="alert-container">
</div>

<x-header/>

@guest
    <x-auth/>
@endguest

<div id="cookie-banner"
     class="fixed bottom-0 left-0 right-0 bg-[#0d0e12] text-sm p-4 shadow-md z-50 text-white opacity-0 translate-y-full transition-all duration-500">
    <div class="container mx-auto flex justify-between items-center">
        <p>Мы используем cookie для улучшения работы сайта. Продолжая использовать сайт, вы соглашаетесь с этим.</p>
        <button id="cookie-accept" class="btn btn-soft btn-success ml-4">Принять</button>
    </div>
</div>

@yield('block')

<div class="app mt-20">
    @yield('content')
</div>

<template id="alert-template">
    <div class="block-alert" style="z-index: 9999">
        <div id="alert" role="alert" class="alert">
            <div id="alert-message" class="text-white cursor-pointer"></div>
        </div>
        <progress id="alert-progress" class="progress" value="0" max="100"></progress>
    </div>
</template>

<x-footer/>

{{--jquery--}}
<script src="/plugins/jquery-3.7.1.min.js"></script>
<script src="/plugins/moment.js"></script>
<script src="/plugins/moment-with-locales.js"></script>

@vite('resources/js/app.js')

<script src="/scripts/alert.js"></script>

@yield('scripts')

@if ($errors->any())
    <script>
        let response = @json($errors->all());
        showAlert(response, 'error');
    </script>
@endif

@if(session('error'))
    <script>
        let response = @json(session('error'));
        showAlert(response, 'error');
    </script>
@endif

@if(session('success'))
    <script>
        let response = @json(session('success'));
        showAlert(response, 'success')
    </script>
@endif
</body>

</html>


