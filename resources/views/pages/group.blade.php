@extends('layout')
@section('title', $group->title)

@section('style')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
    />
@endsection

@section('meta')
    <meta property="og:title" content="{{$group->title}} Присоединяйся!"/>
    <meta property="og:image" content="{{$group->preview ?? $group->video_preview}}"/>
    <meta name="isAdult" content="{{ $group->age_verify ? 'true' : 'false' }}">
@endsection

@section('content')
    <div class="container mx-auto px-5 xl:px-8 lg:px-10 my-6">
        <div class="flex items-start gap-6">
            <div class="flex-1 rounded-xl relative overflow-hidden h-auto sm:h-96">
                <!-- Фоновое изображение -->
                <div class="absolute inset-0 bg-cover bg-center"
                     style="background-image: url('{{ $group->preview ?? $group->video_preview }}')"></div>

                <!-- Оверлей -->
                <div class="absolute inset-0 bg-black opacity-60"></div>

                @if(auth()->user())
                    <div class="flex item-center gap-2 absolute top-4 left-4 z-20">
                        <button id="add-favorite"
                                class="btn btn-circle {{ auth()->user()->favorites->where('group_id', $group->id)->count() ? 'btn-error' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-[1.2em]">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                            </svg>
                        </button>
                        @if(auth()->check() && auth()->id() == $group->user_id)
                            <a href="{{route('groupEdit', ['group' => $group->id])}}"
                               class="btn btn-circle btn-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Аватарка внутри блока -->
                <div class="absolute top-4 right-4 z-20">
                    <a href="{{route('teacher', ['teacher' => $group->user_id])}}">
                        <div
                            class="rounded-full bg-base-300 w-24 h-24 flex items-center justify-center text-center text-sm font-semibold shadow-lg overflow-hidden">
                            <img
                                src="{{$group->user->photo_profile}}"
                                class="rounded-full w-full h-full object-cover"
                                alt="{{$group->user->name}}">
                        </div>
                    </a>
                </div>

                <!-- Контент -->
                <div class="relative z-10 p-6 text-white h-full flex flex-col justify-end pt-30">
                    <h1 class="text-3xl font-bold mb-2 leading-snug">
                        {{$group->title}}
                    </h1>

                    <a href="{{route('teacher', ['teacher' => $group->user_id])}}"
                       class="text-base text-gray-300 mb-2">{{$group->user->name}} {{$group->user->nickname}}</a>

                    <div class="flex flex-wrap gap-2">
                        @foreach($group->categories as $category)
                            <div
                                class="px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm">{{$category->name}}</div>
                        @endforeach
                    </div>

                    @php
                        $onclick = '';
                        if ($group->status_for_user === 'none') {
                            $onclick = auth()->check() ? "onclick='confirmReg.showModal()'" : "onclick='authModal.showModal()'";
                        }
                    @endphp


                    <button class="btn btn-outline mt-3" {!! $onclick !!}>
                        {{ $group->status_for_user === 'confirmed' ? 'Заявка подтверждена' :
                           ($group->status_for_user === 'pending' ? 'Заявка отправлена' : 'Записаться') }}
                    </button>

                    @auth
                        <a href="{{ route('chat', ['user'=> $group->user->id]) }}"
                           class="btn btn-outline mt-3">Связаться</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Блок информации -->
        <div class="bg-base-200 mt-6 p-6 rounded-xl">
            <div
                class="grid grid-cols-1 {{auth()->check() && auth()->user()->id == $group->user_id ?  'md:grid-cols-[3fr_1fr]' : ''}} gap-4 items-start">
                <!-- Левый (основной) блок -->
                @if($group->status_for_user === 'pending')
                    <div class="relative w-full bg-base-100 p-4 rounded-lg shadow-sm h-auto mb-2">
                        <p>
                            Ваша заявка отправлена преподавателю, он с Вами свяжется в чате.
                            Также Вы можете сами написать преподавателю в
                            <a href="{{route('chat', ['user'=> $group->user->id])}}"
                               class="text-primary underline">Чат</a>.
                            Отменить заявку можно в
                            <a href="{{ route('profileGroups') }}" class="text-primary underline">Личном
                                кабинете</a>.
                        </p>
                    </div>
                @endif

                <div class="relative w-full bg-base-100 p-4 rounded-lg shadow-sm h-auto">

                    <p class="desc-container text-base text-gray-600 flex flex-col md:flex-row md:gap-5">
                        @if($group->price)
                            <span class="flex items-center gap-2 mb-2 md:mb-0" id="price-container">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6 price-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                            </svg>
                            <span id="price-span"
                                  class="font-semibold text-gray-800">Цена: {{$group->price}}₽</span>
                        </span>
                        @endif

                        @php
                            use Carbon\Carbon;

                            $startDate = $group->date ? Carbon::parse($group->date)->translatedFormat('d F Y') : null;
                            $endDate = $group->date_end ? Carbon::parse($group->date_end)->translatedFormat('d F Y') : null;
                        @endphp
                        @if($group->date)
                            <span class="flex items-center gap-2 mb-2 md:mb-0" id="time-container">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-6 h-6 schedule-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                            <span id="time-span" class="font-semibold text-gray-800">
                                @if($startDate && $endDate)
                                    {{ $startDate }} — {{ $endDate }}
                                @elseif($startDate)
                                    {{ $startDate }}
                                @else
                                    Дата не указана
                                @endif
                            </span>
                        </span>
                        @endif
                        @if($group->address)
                            <span class="flex items-center gap-2 md:mb-0" id="address-container">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6 address-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                            </svg>

                            <span id="address-span"
                                  class="font-semibold text-gray-800">{{$group->address->studio_name.' — '.$group->address->studio_address}}</span>
                        </span>
                        @endif
                    </p>

                    @php
                        $schedule = json_decode($group->schedule, true);
                        $daysOrder = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

                        $activeDays = collect($schedule)
                            ->filter(fn($v) => $v)
                            ->keys()
                            ->sortBy(fn($day) => array_search($day, $daysOrder))
                            ->implode(', ');
                    @endphp

                    @if($activeDays || $group->time)
                        <div class="schedule-container text-base text-gray-600">
                            <span class="flex w-full items-center gap-2 mt-2 md:mb-0" id="schedule-container">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6 time-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                                <span id="schedule-span" class="font-semibold text-gray-800 w-full">
                                    Расписание: {{ $activeDays }} {{ \Carbon\Carbon::parse($group->time)->translatedFormat('H:i') }}
                                </span>
                            </span>


                            @if($group->duration)
                                <span class="flex w-full items-center gap-2 mt-2 md:mb-0" id="schedule-container">
                                    <svg stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 time-icon">
                                        <path
                                            d="M4.51555 7C3.55827 8.4301 3 10.1499 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3V6M12 12L8 8"
                                            stroke="#000000" stroke-width="1" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                    </svg>
                                    <span id="schedule-span" class="font-semibold text-gray-800 w-full">
                                        Продолжительность: {{ $group->duration }} мин
                                    </span>
                                </span>
                            @endif
                            @if(auth()->check() && auth()->id() == $group->user_id)
                                <span class="flex w-full items-center gap-2 mt-2 md:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                <span id="schedule-span"
                                      class="font-semibold text-gray-800 w-full">{{ $group->views }}</span>
                            </span>
                            @endif
                        </div>
                    @endif

                    <div class="divider"></div>

                    <h2 class="text-lg font-bold mb-2">Информация о наборе</h2>
                    <p class="desc-container text-base max-w-5xl text-gray-600 mb-4 whitespace-pre-wrap"
                       id="description-container">{!! $group->description !!}</p>

                    <div class="divider"></div>

                    <h2 class="text-lg font-bold mb-2">Связаться с преподавателем</h2>
                    <p class="desc-container text-base text-gray-600 mb-4">
                        <span class="flex items-center gap-2">
                            @if(auth()->check())
                                <a href="{{route('chat', ['user'=> $group->user->id])}}"
                                   class="btn btn-neutral">Чат</a>
                            @else
                                <button class="btn btn-neutral" onclick="authModal.showModal()">Чат</button>
                            @endif
                            {{--@if($group->telegram_link)
                                <a href="{{$group->telegram_link}}" target="_blank" class="btn btn-neutral">Telegram</a>
                            @endif
                            <a href="" class="btn btn-neutral">Почта</a>--}}
                        </span>
                    </p>
                </div>


                <!-- Правый (дополнительный) блок -->
                @if(auth()->check() && auth()->user()->id == $group->user_id)
                    <div class="bg-base-100 p-4 rounded-lg shadow-sm h-auto">
                        <div class="flex justify-between items-start">
                            <h2 class="text-lg font-medium mb-2">Группа</h2>
                            <button class="btn btn-square btn-sm rounded-full listUsersBtn"
                                    data-group-id="{{$group->id}}" data-group-name="{{$group->title}}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Список пользователей:</p>

                        <ul class="space-y-4 overflow-y-auto h-[280px]">
                            @forelse($confirmedUsers as $list)
                                <li class="flex items-center justify-between">
                                    <a href="{{route('userInfo', ['user' => $list->id])}}">
                                        <div class="flex items-center space-x-3">
                                            <img class="w-10 h-10 rounded-full"
                                                 src="{{$list->photo_profile}}"
                                                 alt="{{$list->name}}">
                                            <span class="text-gray-700">{{$list->name}} {{$list->nickname}}</span>
                                        </div>
                                    </a>

                                    {{--<button
                                        class="btn btn-square rounded-full btn-sm del-reg"
                                        data-id="{{$group->id}}"
                                        data-desc="{{$group->title}}" data-user="{{$list->id}}"
                                        data-descuser="{{$list->name}} {{$list->nickname}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>--}}

                                </li>

                            @empty
                                <li class="flex items-center justify-center text-gray-400 text-sm h-full w-full">
                                    Пользователей нет
                                </li>
                            @endforelse
                        </ul>

                        @php
                            $current = $group->countUser();
                            $total = $group->count_people;
                            $available = $total - $current;

                            if ($available >= $total * 0.5) {
                                $color = 'text-green-600';
                            } elseif ($available > 0) {
                                $color = 'text-yellow-600';
                            } else {
                                $color = 'text-red-600';
                            }
                        @endphp

                        <div class="mt-4">
                            <p class="text-sm text-gray-600">
                                Свободных мест: <span
                                    class="font-medium {{ $color }}">{{ $current }}/{{ $total }}</span>
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <div class="bg-base-200 mt-6 p-6 rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Видео</h2>
                @if(auth()->check() && auth()->id() == $group->user_id)
                    <button onclick="addVideo.showModal()"
                            class="btn btn-sm btn-neutral z-10">
                        + Добавить видео
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div class="relative aspect-w-9 aspect-h-16 rounded-lg overflow-hidden">
                    <video class="plyr" controls playsinline poster="{{$group->video_preview}}">
                        <source src="{{$group->video_group}}" type="video/mp4"/>
                    </video>
                </div>

                @foreach($group->videoGroups as $video)
                    <div class="relative aspect-w-9 aspect-h-16 rounded-lg overflow-hidden">
                        <video class="plyr" controls playsinline poster="{{$video->preview}}">
                            <source src="{{$video->video}}" type="video/mp4"/>
                        </video>
                    </div>
                @endforeach
            </div>
        </div>

        @if($group->photoGroups->isNotEmpty())
            <div class="bg-base-200 mt-6 p-6 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold">Фотографии</h2>

                    @if(auth()->check() && auth()->id() == $group->user_id)
                        <button onclick="addPhoto.showModal()"
                                class="btn btn-sm btn-neutral z-10">
                            + Добавить фотографии
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($group->photoGroups as $photo)
                        <a data-fancybox="gallery" data-src="{{$photo->photo}}">
                            <div class="relative w-full h-64 rounded-lg overflow-hidden">
                                <img src="{{$photo->photo}}" alt="{{$group->title}}"
                                     class="w-full h-full object-cover shadow-lg">
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection

@section('block')
    <dialog id="confirmReg" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Подтверждение записи</h3>
            <p class="py-4">Вы уверены, что хотите записаться в этот набор?</p>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Отмена</button>
                    <a href="{{route('groupUserReg', ['group' => $group->id])}}" class="btn btn-neutral">Записаться</a>
                </form>
            </div>
        </div>
    </dialog>

    @if(auth()->check() && auth()->id() == $group->user_id)
        <dialog id="addPhoto" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-4">Добавление фотографии</h3>

                <div class="flex items-center justify-center w-full mb-6">
                    <label id="drop-area" for="photo-upload"
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-100 hover:bg-gray-200 transition-all">
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 15a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4M12 3v12m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            <p class="text-sm text-gray-600"><span
                                    class="font-semibold">Нажмите или перетащите файл</span>
                            </p>
                            <p class="text-xs text-gray-400">JPEG, JPG, PNG до 5MB</p>
                        </div>
                        <form action="{{route('groupAddPhoto', ['group' => $group->id])}}" id="postPhoto" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <input id="photo-upload" type="file" multiple class="hidden" name="photos[]"
                                   accept="image/*"/>
                        </form>
                    </label>

                </div>

                <div id="file-preview" class="px-2 text-sm text-gray-700 space-y-1 mt-2"></div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Отмена</button>
                        <button form="postPhoto" type="submit" class="btn btn-neutral">Добавить</button>
                    </form>
                </div>
            </div>
        </dialog>
    @endif

    @if(auth()->check() && auth()->id() == $group->user_id)
        <dialog id="addVideo" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-4">Добавление видео</h3>

                <div class="flex items-center justify-center w-full mb-6">
                    <label id="drop-area-video" for="video-upload"
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-100 hover:bg-gray-200 transition-all">
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 15a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4M12 3v12m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            <p class="text-sm text-gray-600"><span
                                    class="font-semibold">Нажмите или перетащите файл</span>
                            </p>
                            <p class="text-xs text-gray-400">MP4, WEBM, MOV</p>
                            <p class="text-xs text-gray-400 mt-1">Максимальный размер файла: <span
                                    class="font-semibold">70 МБ</span></p>
                        </div>
                        <form action="{{route('groupAddVideo', ['group' => $group->id])}}" id="postVideo" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <input id="video-upload" type="file" accept="video/*" multiple class="hidden"
                                   name="videos[]"/>
                        </form>
                    </label>

                </div>

                <div id="file-preview-video" class="px-2 text-sm text-gray-700 space-y-1 mt-2"></div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Отмена</button>
                        <button form="postVideo" type="submit" class="btn btn-neutral">Добавить</button>
                    </form>
                </div>
            </div>
        </dialog>

        <dialog id="listUsers" class="modal">
            <div class="modal-box max-w-2xl">
                <h3 id="titleGroupList" class="text-lg font-bold">Список пользователей</h3>
                <div class="filt-block mt-3">
                    <label class="floating-label block w-full mb-3">
                        <input type="text" id="searchList" placeholder="Поиск" name="search_list" class="input w-full"/>
                        <span>Поиск</span>
                    </label>

                    <div class="dropdown dropdown-bottom w-full" id="statusDropdown">
                        <fieldset class="fieldset">
                            <div id="dropdownToggle" class="select w-full cursor-pointer">Все</div>
                        </fieldset>
                        <ul
                            class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box text-sm w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
                            <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="filter_app" checked value="all"
                                           class="radio radio-sm"/>
                                    <span>Все</span>
                                </label>
                            </li>

                            <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="filter_app" value="pending"
                                           class="radio radio-sm"/>
                                    <span>Ожидают подтверждения</span>
                                </label>
                            </li>

                            <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="filter_app" value="confirmed"
                                           class="radio radio-sm"/>
                                    <span>Подтвержденные</span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="list-users" id="list-user-container">
                    <ul class="list">

                    </ul>
                </div>
                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Закрыть</button>
                    </form>
                </div>
            </div>
        </dialog>

        <dialog id="confirmDeleteList" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Подтверждение действия</h3>
                <p class="py-4">Вы уверены что хотите отклонить заявку пользователю: <span id="userDeleteList"
                                                                                           class="font-semibold text-red-500"></span>?
                </p>
                <div class="modal-action">
                    <form method="dialog">
                        <button id="acceptDeleteList" class="btn btn-error">Продолжить</button>
                        <button class="btn">Отмена</button>
                    </form>
                </div>
            </div>
        </dialog>
    @endif

    @if(auth()->check() && auth()->id() == $group->user_id)
        <dialog id="cancelReg" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Отмена записи</h3>
                <p class="py-4">
                    Вы уверены что хотите отменить запись в наборе:
                    <span id="groupNameSpan" class="text-red-600 font-bold"></span>,
                    Пользователя:
                    <span id="userNameSpan" class="text-red-600 font-bold"></span>
                </p>
                <div class="modal-action">
                    <form method="POST" action="{{route('deleteUserTeacher')}}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="group_id" id="cancelGroupId">
                        <input type="hidden" name="user_id" id="cancelUserId">
                        <button type="submit" class="btn btn-error">Подтвердить отмену</button>
                        <button type="button" class="btn" id="cancelClose">Отмена</button>
                    </form>
                </div>
            </div>
        </dialog>
    @endif

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const meta = document.querySelector('meta[name="isAdult"]');
            const isAdult = meta && meta.content === "true";

            const ageConfirm = localStorage.getItem('ageConfirmed');

            if (isAdult && ageConfirm !== 'true') {
                document.getElementById('ageVerify').showModal();
            }


            @if(auth()->check() && auth()->id() == $group->user_id)
            const dropdown = document.getElementById('statusDropdown');
            const toggle = document.getElementById('dropdownToggle');

            toggle.addEventListener('click', function () {
                dropdown.classList.toggle('dropdown-open');
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('dropdown-open');
                }
            });
            @endif
        });
    </script>

    <script>
        Fancybox.bind('[data-fancybox]', {});
    </script>

    <script>
        @if(auth()->check())
        document.getElementById('add-favorite').addEventListener('click', async function () {
            const favorite = this;

            try {
                const response = await fetch('{{ route('groupAddFavorite', $group->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    return showAlert(data.message || 'Произошла ошибка', 'error');
                }

                if (data.action === 'add') {
                    favorite.classList.add('btn-error');
                } else {
                    favorite.classList.remove('btn-error');
                }

                showAlert(data.message, 'success');

            } catch (error) {
                showAlert(error.message, 'error');
            }
        });
        @endif
    </script>

    @if(auth()->check() && auth()->id() == $group->user_id)
        <script>
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('photo-upload');
            const preview = document.getElementById('file-preview');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.classList.add('bg-gray-200', 'border-gray-400');
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.classList.remove('bg-gray-200', 'border-gray-400');
            });

            function updatePreview(files) {
                preview.innerHTML = '';
                Array.from(files).forEach(file => {
                    const item = document.createElement('div');
                    item.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    preview.appendChild(item);
                });
            }

            dropArea.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updatePreview(files);
                }
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    updatePreview(fileInput.files);
                }
            });
        </script>

        <script>
            const dropAreaVideo = document.getElementById('drop-area-video');
            const fileInputVideo = document.getElementById('video-upload');
            const previewVideo = document.getElementById('file-preview-video');
            const MAX_SIZE = 70 * 1024 * 1024;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropAreaVideo.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropAreaVideo.classList.add('bg-gray-200', 'border-gray-400');
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropAreaVideo.classList.remove('bg-gray-200', 'border-gray-400');
            });

            function updatePreviewVideo(files) {
                previewVideo.innerHTML = '';
                Array.from(files).forEach(file => {
                    const item = document.createElement('div');
                    item.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    previewVideo.appendChild(item);
                });
            }

            function filterFilesBySize(files) {
                for (const file of files) {
                    if (file.size > MAX_SIZE) {
                        addVideo.close();
                        showAlert('Максимальный размер файла 70МБ', 'error');
                        return false;
                    }
                }
                return true;
            }

            dropAreaVideo.addEventListener('drop', e => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    if (filterFilesBySize(files)) {
                        fileInputVideo.files = files;
                        updatePreviewVideo(files);
                    } else {
                        fileInputVideo.value = '';
                        previewVideo.innerHTML = '';
                    }
                }
            });

            fileInputVideo.addEventListener('change', () => {
                if (fileInputVideo.files.length > 0) {
                    if (filterFilesBySize(fileInputVideo.files)) {
                        updatePreviewVideo(fileInputVideo.files);
                    } else {
                        fileInputVideo.value = '';
                        previewVideo.innerHTML = '';
                    }
                }
            });
        </script>

        <script>
            const addPhotoDialog = document.getElementById('addPhoto');
            const addVideoDialog = document.getElementById('addVideo');

            document.getElementById('postPhoto').addEventListener('submit', function () {
                const loadingText = document.getElementById('loading-post');
                loadingText.innerHTML = 'Идёт загрузка фотографий, пожалуйста подождите...';
                loadingText.classList.remove('hidden');

                if (addPhotoDialog?.close) addPhotoDialog.close();
            });

            document.getElementById('postVideo').addEventListener('submit', function () {
                const loadingText = document.getElementById('loading-post');
                loadingText.innerHTML = 'Идёт загрузка видео, пожалуйста подождите...';
                loadingText.classList.remove('hidden');

                if (addVideoDialog?.close) addVideoDialog.close();
            });
        </script>

        <script>
            const modalCancel = document.getElementById('cancelReg');
            const groupNameSpan = document.getElementById('groupNameSpan');
            const userNameSpan = document.getElementById('userNameSpan');
            const cancelGroupId = document.getElementById('cancelGroupId');
            const cancelUserId = document.getElementById('cancelUserId');
            const cancelForm = document.getElementById('cancelForm');

            document.querySelectorAll('.del-reg').forEach(button => {
                button.addEventListener('click', (event) => {
                    const btn = event.currentTarget;
                    const groupId = btn.dataset.id;
                    const groupDesc = btn.dataset.desc;
                    const userId = btn.dataset.user;
                    const userDesc = btn.dataset.descuser;

                    userNameSpan.textContent = userDesc;
                    groupNameSpan.textContent = groupDesc;
                    cancelGroupId.value = groupId;
                    cancelUserId.value = userId;

                    modalCancel.showModal();
                });
            });

            document.getElementById('cancelClose').addEventListener('click', () => {
                modalCancel.close();
            });

        </script>
    @endif

    @vite(['resources/js/listUsersFetch.js'])
@endsection
