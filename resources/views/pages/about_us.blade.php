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
            animation: fadeInUp 1s ease-out forwards;
        }

        .animate-delay-1 {
            animation-delay: 0.2s;
        }

        .animate-delay-2 {
            animation-delay: 0.5s;
        }

        .animate-delay-3 {
            animation-delay: 1s;
        }
    </style>
@endsection

@section('app-margin', 'mt-16')

@section('content')
    <div class="hero h-screen relative overflow-hidden">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover">
            <source src="/videos/IMG_2534.MOV" type="video/mp4">
            Ваш браузер не поддерживает видео.
        </video>

        <div class="hero-overlay absolute inset-0 bg-black opacity-50"></div>

        <div class="hero-content relative z-10 text-neutral-content text-center py-30 mb-25">
            <div class="max-w-md">
                <img src="/images/logo-hero.png" alt="logo"
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
            <h2 class="text-4xl md:text-5xl font-bold animate-fade-up">
                Танцы <br> это для всех!
            </h2>
            <div class="divider mx-auto my-10 w-1/2"></div>

            <div class="grid grid-cols-1 gap-5">
                <!-- 1 ряд -->
                <div class="grid sm:grid-cols-[2fr_1fr] gap-5">
                    <!-- Текстовый блок -->
                    <div class="bg-black text-white rounded-2xl h-64 sm:h-80 flex items-center justify-center text-center px-4">
                        <p class="text-xl sm:text-2xl font-semibold">
                            Мы верим, что танцы – это для всех!
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
                                Мы поможем найти то, что подойдёт именно тебе
                            </p>
                        </div>
                    </div>
                    <!-- Текстовый блок -->
                    <div class="bg-black text-white rounded-2xl h-64 sm:h-80 flex items-center justify-center text-center px-4">
                        <p class="text-xl sm:text-2xl font-semibold">
                            Мы поможем сделать первые шаги
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
@endsection
