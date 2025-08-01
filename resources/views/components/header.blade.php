<div class="navbar bg-base-100 shadow-sm px-5 fixed" style="z-index: 9998">
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
            </div>
            <ul
                tabindex="0"
                class="menu dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
                style="z-index: 9998">
                <li><a href="{{route('index')}}">Главная</a></li>
                <li><a href="{{route('groups')}}">Поиск</a></li>
                <li><a href="{{route('teachers')}}">Преподаватели</a></li>
                @auth
                    <li><a href="{{route('profileFavorites')}}">Избранное</a></li>
                    <li><a href="{{route('profile')}}">Личный профиль</a></li>
                    <li><a href="{{route('chat')}}">Чаты</a></li>
                @endauth
            </ul>
        </div>
        <a href="{{route('index')}}" class="btn btn-ghost text-xl px-1">Все танцы</a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="{{route('index')}}">Главная</a></li>
            <li><a href="{{route('groups')}}">Поиск</a></li>
            <li><a href="{{route('teachers')}}">Преподаватели</a></li>
            @auth
                <li><a href="{{route('profileFavorites')}}">Избранное</a></li>
                <li><a href="{{route('profile')}}">Личный профиль</a></li>
                <li><a href="{{route('chat')}}">Чаты</a></li>
            @endauth
        </ul>
    </div>
    <div class="navbar-end gap-1">
        <a href="{{route('groups')}}" class="btn btn-ghost btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
        </a>
        @guest
            <a class="btn" onclick="authModal.showModal()">Войти</a>
        @endguest
        @auth
            @if(auth()->user()->roles->contains('slug', 'teacher'))
                <a href="{{route('profileMyGroups')}}?modal=open" class="btn btn-circle btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </a>
            @endif

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="indicator btn btn-ghost btn-circle">
                    @if($countNotifications)
                        <span class="indicator-item indicator-top indicator-start badge badge-sm badge-secondary">
                            {{ $countNotifications > 99 ? '99+' : $countNotifications }}
                        </span>
                    @endif

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                </div>
                <ul tabindex="0"
                    class="dropdown-content z-[9999] mt-3 w-80 menu bg-base-100 rounded-box shadow p-2">
                    <li class="text-sm text-gray-400 mb-2 px-2">Уведомления</li>
                    @forelse($notifications as $note)
                        <li>
                            <a href="{{route('markRead', ['idNotify' => $note->id])}}">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{$note->data['title']}}</span>
                                    <span
                                        class="text-xs text-gray-500">{{$note->data['message']}}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-2 text-sm text-gray-500 mb-2">Уведомлений нет</li>
                    @endforelse
                    @if($notifications->count() >= 3)
                        <li class="text-center text-xs text-neutral mt-1">
                            <a href="{{route('profileNotifications')}}">Смотреть все</a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img
                            alt="{{ Auth::user()->name }}"
                            src="{{ Auth::user()->photo_profile }}"/>
                    </div>
                </div>
                <ul
                    tabindex="0"
                    class="menu dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
                    style="z-index: 9998">
                    <li><a href="{{route('profile')}}">Личный профиль</a></li>
                    @hasAccess('platform.index')
                    <li><a href="{{route('platform.group')}}" target="_blank">Админ-панель</a></li>
                    @endhasAccess
                    <li><a href="{{route('signOut')}}">Выйти</a></li>
                </ul>
            </div>
        @endauth
    </div>
</div>
