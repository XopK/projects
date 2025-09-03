@extends('layout')
@section('title', 'Все танцы')

@section('style')
    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            opacity: 0;
            animation: fadeInUp 1s ease-out forwards;
        }

        .animate-delay-1 { animation-delay: 0.2s; }
        .animate-delay-2 { animation-delay: 0.5s; }
        .animate-delay-3 { animation-delay: 1s; }

        /* Для секций */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
        }
        .reveal.active {
            animation: fadeInUp 1s ease-out forwards;
        }

    </style>
@endsection

@section('app-margin', 'mt-16')

@section('content')
    <div class="hero h-screen relative overflow-hidden">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover">
            <source src="/videos/IMG_2534.mp4" type="video/mp4">
            <source src="/videos/IMG_2534.webm" type="video/webm">
            Ваш браузер не поддерживает видео.
        </video>

        <div class="hero-overlay absolute inset-0 bg-black opacity-50"></div>

        <div class="hero-content relative z-10 text-neutral-content text-center py-30 mb-25">
            <div class="max-w-md">
                <img src="/images/logo-hero.png" alt="Все Танцы"
                     class="w-80 mx-auto opacity-0 animate-fadeInUp animate-delay-1">
                <p class="my-20 font-bold text-xl opacity-0 animate-fadeInUp animate-delay-2">
                    Выбирай из огромного количества различных направлений и преподавателей!
                </p>
                <button class="uppercase btn-hero opacity-0 animate-fadeInUp animate-delay-3">
                    Найти и записаться
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="dancing-is-for-everyone text-center">
            <h2 class="text-4xl md:text-5xl font-bold reveal">
                Танцы <br> это для всех!
            </h2>

            <div class="divider mx-auto my-10 w-1/2 reveal"></div>

            <div class="grid grid-cols-1 gap-5 reveal">
                <!-- 1 ряд -->
                <div class="grid sm:grid-cols-[2fr_1fr] gap-5">
                    <!-- Текстовый блок -->
                    <div
                        class="bg-black text-white rounded-2xl h-64 sm:h-80 flex items-center justify-center text-center px-4">
                        <p class="text-xl sm:text-2xl font-semibold">
                            Мы верим, что танцы - это для всех!
                        </p>
                    </div>
                    <!-- Фото с затемнением -->
                    <div class="relative rounded-2xl overflow-hidden h-64 sm:h-80">
                        <img src="/images/dancing1.jpg" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-white text-center px-4">
                            <p class="text-xl sm:text-2xl font-semibold">
                                Мы знаем, что начать можно в любом возрасте!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2 ряд -->
                <div class="grid sm:grid-cols-[1fr_2fr] gap-5">
                    <!-- Фото с затемнением -->
                    <div class="relative rounded-2xl overflow-hidden h-64 sm:h-80">
                        <img src="/images/dancing2.jpg" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-white text-center px-4">
                            <p class="text-xl sm:text-2xl font-semibold">
                                Мы поможем найти то, что подойдет именно тебе!
                            </p>
                        </div>
                    </div>
                    <!-- Текстовый блок -->
                    <div
                        class="bg-black text-white rounded-2xl h-64 sm:h-80 flex items-center justify-center text-center px-4">
                        <p class="text-xl sm:text-2xl font-semibold">
                            Мы поможем сделать первые шаги.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="advantages text-center mt-20">
            <h2 class="text-4xl md:text-5xl font-bold reveal">
                Все возможности в одном сервисе
            </h2>

            <p class="mt-6 max-w-3xl mx-auto text-base sm:text-lg text-gray-600 leading-relaxed reveal">
                Мы собираем все актуальные анонсы групп, классов, интенсивов, индивидуальных занятий и привозных
                мастер-классов на одном сервисе с удобным поиском по фильтрам!
                Просто выбери нужное направление, уровень сложности, категорию занятий или локацию — сервис покажет тебе
                все подходящие группы!
            </p>

            <div class="divider mx-auto my-10 w-1/2 reveal"></div>

            <div class="grid gap-6 px-4 md:px-8 md:grid-cols-2 reveal">
                <!-- Преимущество 1 -->
                <div
                    class="flex items-start gap-4 p-6 rounded-2xl shadow bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div
                        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-blue-100 flex-shrink-0">
                        <!-- Squares2x2Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6 md:w-8 md:h-8 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 4.5h4.5v4.5h-4.5V4.5zM3.75 14.25h4.5v4.5h-4.5v-4.5zM13.5 4.5h6.75v6.75H13.5V4.5zM13.5 14.25h6.75v6.75H13.5v-6.75z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold">Все в одном месте!</h3>
                        <p class="text-gray-600">Анонсы групп, классов, интенсивов и мастер-классов собраны в одном
                            удобном сервисе.</p>
                    </div>
                </div>

                <!-- Преимущество 2 -->
                <div
                    class="flex items-start gap-4 p-6 rounded-2xl shadow bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div
                        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-green-100 flex-shrink-0">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6 md:w-8 md:h-8 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5"/>
                        </svg>

                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold">Удобный поиск по фильтрам</h3>
                        <p class="text-gray-600">Фильтруй занятия по уровню, направлению, категории или локации — находи
                            только то, что подходит тебе.</p>
                    </div>
                </div>

                <!-- Преимущество 3 -->
                <div
                    class="flex items-start gap-4 p-6 rounded-2xl shadow bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div
                        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-purple-100 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6 md:w-8 md:h-8 text-purple-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                        </svg>

                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold">Удобная система уведомлений</h3>
                        <p class="text-gray-600">Напоминания о записях и уведомления об изменениях всегда приходят
                            вовремя.</p>
                    </div>
                </div>

                <!-- Преимущество 4 -->
                <div
                    class="flex items-start gap-4 p-6 rounded-2xl shadow bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div
                        class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-red-100 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6 md:w-8 md:h-8 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>

                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold">Чаты</h3>
                        <p class="text-gray-600">Общайся с преподавателями напрямую и записывайся на занятия в пару
                            кликов.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sign h-80 relative overflow-hidden bg-black flex flex-col items-center justify-center px-4 text-center reveal mt-15">
        <h2 class="text-white text-lg font-bold max-w-2xl p-4">
            Записывайся на занятия в чате с преподавателем, добавляй в избранное, пробуй разное и ищи свое!
        </h2>

        <button class="uppercase btn-hero mt-6">
            Найти и записаться
        </button>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const reveals = document.querySelectorAll(".reveal");

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            reveals.forEach(el => observer.observe(el));
        });
    </script>
@endsection
