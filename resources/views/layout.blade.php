<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Top.Mail.Ru counter -->
    <script type="text/javascript">
        var _tmr = window._tmr || (window._tmr = []);
        _tmr.push({id: "3685223", type: "pageView", start: (new Date()).getTime()});
        (function (d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script");
            ts.type = "text/javascript";
            ts.async = true;
            ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function () {
                var s = d.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(ts, s);
            };
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "tmr-code");
    </script>
    <noscript>
        <div><img src="https://top-fwz1.mail.ru/counter?id=3685223;js=na" style="position:absolute;left:-9999px;"
                  alt="Top.Mail.Ru"/></div>
    </noscript>
    <!-- /Top.Mail.Ru counter -->

    @yield('meta')

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="/styles/style.css">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css"/>

    @yield('style')

    <title>@yield('title', 'Все танцы')</title>
</head>
<body class="font-play">

<div id="loader">
    <span id="loading-post" class="hidden"></span>
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

<dialog id="ageVerify" class="modal" style="display: flex; align-items: center; justify-content: center;">
    <div class="modal-box text-center">
        <h3 class="text-lg font-bold mb-4">Подтверждение возраста</h3>
        <img src="/images/18.svg" class="size-25 mx-auto mb-3" alt="18.svg">
        <p class="py-4">Этот пост доступен только для лиц, достигших 18 лет.</p>
        <div class="modal-action justify-center">
            <form method="dialog" class="flex gap-4 justify-center">
                <button id="ageVerifyTrue" class="btn btn-success">Мне 18+</button>
                <button id="ageVerifyFalse" class="btn btn-error">Мне нет 18+</button>
            </form>
        </div>
    </div>
</dialog>

@yield('block')

<div class="app mt-20">
    @yield('content')
</div>

<!-- Нижнее меню -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden">
    <div class="flex justify-evenly items-center py-3">
        <!-- Домой -->
        <a href="{{route('index')}}" class="flex flex-col items-center flex-1 text-gray-700 hover:text-black">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 9.75L12 3l9 6.75V21a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 21V9.75z"/>
            </svg>
            <span class="text-[10px]">Главная</span>
        </a>

        <!-- Поиск -->
        <a href="{{route('groups')}}" class="flex flex-col items-center flex-1 text-gray-700 hover:text-black">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
            </svg>
            <span class="text-[10px]">Поиск</span>
        </a>

        @auth
            @if(auth()->user()->roles->contains('slug', 'teacher'))
                <!-- Добавить -->
                <a href="{{route('profileMyGroups')}}?modal=open"
                   class="flex flex-col items-center flex-1 text-gray-700 hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-[10px]">Добавить</span>
                </a>
            @endif

            <!-- Чаты -->
            <a href="{{route('chat')}}" class="flex flex-col items-center flex-1 text-gray-700 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                </svg>
                <span class="text-[10px]">Сообщения</span>
            </a>

            <!-- Профиль -->
            <a href="{{route('profile')}}" class="flex flex-col items-center flex-1 text-gray-700 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>

                <span class="text-[10px]">Профиль</span>
            </a>
        @endauth
    </div>
</nav>

<template id="alert-template">
    <div class="block-alert" style="z-index: 9999">
        <div id="alert" role="alert" class="alert">
            <div id="alert-message" class="text-white cursor-pointer"></div>
        </div>
        <progress id="alert-progress" class="progress" value="0" max="100"></progress>
    </div>
</template>

<x-footer/>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments)
        };
        m[i].l = 1 * new Date();
        for (var j = 0; j < document.scripts.length; j++) {
            if (document.scripts[j].src === r) {
                return;
            }
        }
        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js?id=103837213', 'ym');

    ym(103837213, 'init', {
        ssr: true,
        webvisor: true,
        clickmap: true,
        ecommerce: "dataLayer",
        accurateTrackBounce: true,
        trackLinks: true
    });
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/103837213" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

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


