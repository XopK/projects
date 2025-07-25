@extends('layout')
@section('title', 'Все танцы')

@section('style')
@endsection

@section('content')
    <div class="container relative mx-auto px-5 max-w-screen-xl">

        <div class="flex justify-center">
            <section class="lenta space-y-6 mb-8 w-full max-w-md" id="groups-grid">
                <!-- Пост 1 -->

            </section>
        </div>

        <div id="scrollLoader" class="h-10 mb-4 w-full flex justify-center items-center text-gray-500">
            <span class="loading loading-dots loading-xl"></span>
        </div>

    </div>
@endsection

@section('scripts')
    @vite('resources/js/fetchGroups.js')
@endsection
