@extends('layout')
@section('title', $user->name . ' ' . $user->nickname)

@section('style')

@endsection

@section('content')
    <div class="container mx-auto px-10 my-7">
        <div class="mb-8 text-center">
            <!-- Аватарка -->
            <div class="avatar justify-center mb-4">
                <div class="w-24 h-24 rounded-full ring ring-gray-500 ring-offset-base-100 ring-offset-2">
                    <img src="{{ $user->photo_profile }}" alt="{{ $user->name }}">
                </div>
            </div>

            <!-- Имя и описание -->
            <h1 class="text-3xl font-bold">{{ $user->name . ' ' . $user->nickname ?? 'Данные отсутствуют' }}</h1>
            <p class="text-gray-500">Пользовательская страница</p>
        </div>

        <div class="max-w-xl mx-auto bg-base-100 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold">Информация</h2>

            <div class="divider"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Email</p>
                    <p class="text-lg font-medium">{{ $user->email ?? 'Не указано' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Телефон</p>
                    <p class="text-lg font-medium">{{ $user->phone ?? 'Не указано' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Telegram</p>
                    <p class="text-lg font-medium">{{ $user->username_telegram ?? 'Не указано' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Дата регистрации</p>
                    <p class="text-lg font-medium">{{ $user->created_at->format('d.m.Y') ?? 'Неизвестно' }}</p>
                </div>
            </div>

            <div class="divider"></div>

            <div class="flex justify-center">
                <a href="{{route('chat', ['user'=> $user->id])}}" class="btn btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="max-w-3xl mx-auto bg-base-100 shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-xl text-center font-semibold mb-4">Записи пользователя в ваших наборах</h2>

            <form action="{{route('userInfo', ['user' => $user->id])}}" method="get">
                <div class="sort-block mb-3 px-2 flex justify-between items-center">
                    <div class="w-full">
                        <label class="floating-label">
                            <input type="text" placeholder="Поиск" id="search" name="search"
                                   value="{{request('search')}}"
                                   class="input w-full"/>
                            <span>Поиск</span>
                        </label>
                    </div>
                    <div class="dropdown dropdown-left dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost rounded-full m-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                            </svg>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li><a href="?sort_field=created_at&sort_direction=desc&search={{ request('search') }}"
                                   class="sort-option">Сначала новые</a></li>
                            <li><a href="?sort_field=created_at&sort_direction=asc&search={{ request('search') }}"
                                   class="sort-option">Сначала старые</a></li>
                            <li><a href="?sort_field=title&sort_direction=asc&search={{ request('search') }}"
                                   class="sort-option">По алфавиту А-Я</a></li>
                            <li><a href="?sort_field=title&sort_direction=desc&search={{ request('search') }}"
                                   class="sort-option">По алфавиту Я-А</a></li>
                        </ul>

                    </div>
                </div>
            </form>

            <ul class="list" id="groups-list">
                @forelse($groups as $group)
                    <li class="list-row flex flex-col sm:flex-row items-center sm:justify-between gap-4 p-2 w-full mb-4 sm:mb-0">
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                            <div class="w-full sm:w-32 sm:h-32 rounded-box overflow-hidden flex-shrink-0">
                                <img class="w-full h-auto sm:w-32 sm:h-32 object-cover"
                                     src="{{$group->preview ?? $group->video_preview}}"/>
                            </div>
                            <div class="flex flex-col text-center sm:text-left w-full">
                                <div class="font-medium text-lg">{{$group->title}}
                                </div>
                                <div
                                    class="description_group text-sm font-semibold opacity-60 mt-2 sm:mt-0 truncate w-64 sm:w-96 mx-auto sm:mx-0">
                                    {{$group->description}}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">

                            @if(!$group->statusConf)
                                <div class="tooltip tooltip-bottom sm:tooltip-top">
                                    <div class="tooltip-content">
                                        <div class="text-base">Вы не подтвердили запись в набор у этого пользователя.</div>
                                    </div>
                                    <button
                                        class="btn btn-circle btn-warning rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Страница группы">
                                <a href="{{route('group', ['group' => $group->id])}}"
                                   class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top"
                                 data-tip="Удалить пользователя из группы">
                                <button
                                    class="btn btn-square rounded-full del-reg" data-id="{{$group->id}}"
                                    data-desc="{{$group->title}}" data-user="{{$user->id}}"
                                    data-descuser="{{$user->name}} {{$user->nickname}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="w-full text-center text-lg text-gray-500 py-5">
                        Группы отсутствуют
                    </div>
                @endforelse

                @if($groups->hasPages())
                    <div class="mt-3">
                        {{ $groups->links() }}
                    </div>
                @endif
            </ul>
        </div>
    </div>
@endsection

@section('block')
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
@endsection

@section('scripts')

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
@endsection
