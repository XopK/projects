@extends('layout')
@section('title', 'Все танцы')

@section('style')
@endsection

@section('content')
    <div
        class="hero h-[80vh]"
        style="background-image: url('/images/fatherpolitics_c2a9laura_gauch.png');">
        <div class="hero-overlay"></div>
        <div class="hero-content text-neutral-content text-center py-30">
            <div class="max-w-md">
                <img src="/images/logo.png" alt="logo">
                <p class="my-20 font-bold text-xl">
                    Выбирай из огромного количества различных направлений и преподавателей!
                </p>
                <button class="uppercase btn-hero">Найти и записаться</button>
            </div>
        </div>
    </div>

    <div class="container mx-auto py-16 px-6">
        <h2 class="text-4xl font-bold text-center mb-10">
            Танцы <br> это для всех!
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1 -->
            <div class="bg-black text-white rounded-2xl p-8 flex items-center justify-center relative">
                <p class="text-center font-medium">
                    Мы верим, что танцы – это для всех!
                </p>
            </div>

            <!-- 2 -->
            <div class="rounded-2xl overflow-hidden relative">
                <img src="/images/dancing1.png" alt="card2" class="w-full h-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center p-8">
                    <p class="text-white font-medium text-center drop-shadow-lg">
                        Мы знаем, что начать можно в любом возрасте!
                    </p>
                </div>
            </div>

            <!-- 3 -->
            <div class="rounded-2xl overflow-hidden relative">
                <img src="/images/card1.png" alt="card1" class="w-full h-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center p-8">
                    <p class="text-white font-medium text-center drop-shadow-lg">
                        Мы поможем найти то, что подойдет именно тебе
                    </p>
                </div>
            </div>

            <!-- 4 -->
            <div class="bg-black text-white rounded-2xl p-8 flex items-center justify-center relative">
                <p class="text-center font-medium">
                    Мы поможем сделать первые шаги
                </p>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
