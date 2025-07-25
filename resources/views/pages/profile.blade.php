@extends('layout')
@section('title', 'Личный профиль')

@section('style')

@endsection

@section('content')
    <x-profile-menu>
        <!-- Изменить данные -->
        <div class="card shadow-md p-5 rounded-lg mb-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <div class="text-2xl font-bold select-none">Изменить данные</div>
            </div>
            <form action="{{route('profileUpdate')}}" method="post" id="formEditUser">
                @csrf
                <x-elements.input label="Имя" name="name" value="{{Auth::user()->name}}" type="text" id="name"/>
                <x-elements.input label="Никнейм или фамилия" name="nickname" value="{{Auth::user()->nickname}}"
                                  type="text"
                                  id="nickname"/>
                <x-elements.input label="Почта" name="email" value="{{Auth::user()->email}}" type="email"
                                  id="email"/>
                <x-elements.input label="Дата рождения" name="birthday" value="{{Auth::user()->birthday}}" type="date"
                                  id="birthday" max="{{date('Y-m-d')}}"/>
                <x-elements.input label="Телефон" name="phone" value="{{Auth::user()->phone}}" type="text"
                                  id="phoneInput"/>

                <input type="hidden" name="code_phone" id="full-code"/>
                <button class="btn btn-neutral mt-5 p-5" type="submit">
                    <span id="spinnerEditUser" class="loading loading-spinner hidden"></span>
                    Сохранить
                </button>
            </form>
        </div>


        <!-- Социальные сети -->
        <div class="card shadow-md p-5 rounded-lg mt-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"/>
                </svg>
                <div class="text-2xl font-bold select-none">Социальные сети</div>
            </div>

            <!-- Кнопки для подключения соцсетей -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Telegram -->
                @if(!Auth::user()->chat_id_telegram)
                    <a href="{{route('profileTelegram')}}" target="_blank"
                       class="btn w-full sm:w-auto gap-2 p-6 justify-start">
                    <span class="[&>svg]:h-7 [&>svg]:w-7 [&>svg]:fill-[#0088cc]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512">
                            <path
                                d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z"/>
                        </svg>
                    </span>
                        Подключить Telegram
                    </a>
                @else
                    <a href="{{route('profileTelegramUnlink')}}"
                       class="btn btn-outline w-full sm:w-auto gap-2 p-6 justify-start">
                    <span class="[&>svg]:h-7 [&>svg]:w-7 [&>svg]:fill-[#0088cc]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512">
                            <path
                                d="M248 8C111 8 0 119 0 256S111 504 248 504 496 393 496 256 385 8 248 8zM363 176.7c-3.7 39.2-19.9 134.4-28.1 178.3-3.5 18.6-10.3 24.8-16.9 25.4-14.4 1.3-25.3-9.5-39.3-18.7-21.8-14.3-34.2-23.2-55.3-37.2-24.5-16.1-8.6-25 5.3-39.5 3.7-3.8 67.1-61.5 68.3-66.7 .2-.7 .3-3.1-1.2-4.4s-3.6-.8-5.1-.5q-3.3 .7-104.6 69.1-14.8 10.2-26.9 9.9c-8.9-.2-25.9-5-38.6-9.1-15.5-5-27.9-7.7-26.8-16.3q.8-6.7 18.5-13.7 108.4-47.2 144.6-62.3c68.9-28.6 83.2-33.6 92.5-33.8 2.1 0 6.6 .5 9.6 2.9a10.5 10.5 0 0 1 3.5 6.7A43.8 43.8 0 0 1 363 176.7z"/>
                        </svg>
                    </span>
                        Отключить Telegram
                    </a>

                    <p class="text-sm w-md text-gray-500">
                        Не забудьте включить уведомления в Telegram и на сайте, чтобы получать важные сообщения!
                    </p>
                @endif

                {{--<a href=""
                   class="btn w-full sm:w-auto gap-2 p-6 justify-start">
                    <span class="[&>svg]:h-7 [&>svg]:w-7 [&>svg]:fill-[#45668e]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                          <path
                              d="M31.5 63.5C0 95 0 145.7 0 247V265C0 366.3 0 417 31.5 448.5C63 480 113.7 480 215 480H233C334.3 480 385 480 416.5 448.5C448 417 448 366.3 448 265V247C448 145.7 448 95 416.5 63.5C385 32 334.3 32 233 32H215C113.7 32 63 32 31.5 63.5zM75.6 168.3H126.7C128.4 253.8 166.1 290 196 297.4V168.3H244.2V242C273.7 238.8 304.6 205.2 315.1 168.3H363.3C359.3 187.4 351.5 205.6 340.2 221.6C328.9 237.6 314.5 251.1 297.7 261.2C316.4 270.5 332.9 283.6 346.1 299.8C359.4 315.9 369 334.6 374.5 354.7H321.4C316.6 337.3 306.6 321.6 292.9 309.8C279.1 297.9 262.2 290.4 244.2 288.1V354.7H238.4C136.3 354.7 78 284.7 75.6 168.3z"/>
                        </svg>
                    </span>
                    Подключить VK
                </a>


                <a href=""
                   class="btn w-full sm:w-auto gap-2 p-6 justify-start">
                    <span class="[&>svg]:h-7 [&>svg]:w-7 [&>svg]:fill-[#128c7e]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 448 512">
                          <path
                              d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                        </svg>
                    </span>
                    Подключить Whatsapp
                </a>--}}

            </div>
        </div>


        <!-- Пароль -->
        <div class="card shadow-md p-5 rounded-lg mt-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
                <div class="text-2xl font-bold select-none">Изменить пароль</div>
            </div>

            <form action="{{route('updatePassword')}}" method="POST">
                @csrf
                @method('PUT')
                <x-elements.input name="old_password" id="old_password" isRequired="true" label="Cтарый пароль"
                                  type="password"/>
                <x-elements.input name="new_password" id="new_password" isRequired="true" label="Новый пароль"
                                  type="password"/>
                <x-elements.input name="confirm_new_password" id="confirm_new_password" isRequired="true"
                                  label="Подтверждение пароля" type="password"/>

                <button class="btn btn-neutral mt-5 p-5" type="submit">Изменить</button>
            </form>
        </div>

        <!-- Уведомления -->
        <div class="card shadow-md p-5 rounded-lg mt-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                </svg>
                <div class="text-2xl font-bold select-none">Уведомления</div>
            </div>

            <div class="flex flex-wrap gap-4">

                @if(Auth::user()->chat_id_telegram)
                    <div class="card w-full bg-base-100 card-md shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">Telegram</h2>
                            <p>Наш бот будет информировать вас о новых сообщениях.</p>
                            <div class="justify-end card-actions">
                                <input type="checkbox"
                                       {{Auth::user()->notify_tg ? 'checked' : ''}} data-url="{{ route('settingNotify', 'notify_tg') }}"
                                       name="notify_tg"
                                       class="toggle notify-toggle"/>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card w-full bg-base-100 card-md shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Email</h2>
                        <p>Вы будете получать уведомления о новых сообщениях на вашу электронную почту.</p>
                        <div class="justify-end card-actions">
                            <input type="checkbox"
                                   {{Auth::user()->notify_email ? 'checked' : ''}} data-url="{{ route('settingNotify', 'notify_email') }}"
                                   name="notify_email"
                                   class="toggle notify-toggle"/>
                        </div>
                    </div>
                </div>

                <div class="card w-full bg-base-100 card-md shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Push уведомления</h2>
                        <p>Получайте уведомления в браузере о новых событиях.</p>
                        <div class="justify-end card-actions">
                            <input type="checkbox"
                                   {{Auth::user()->notify_site ? 'checked' : ''}} data-url="{{ route('settingNotify', 'notify_site') }}"
                                   name="notify_site"
                                   class="toggle notify-toggle"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-profile-menu>
@endsection

@section('block')
    <dialog id="phoneEdit" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Изменение номера телефона</h3>
            <p class="py-4">Мы отправили код на ваш новый номер — проверьте сообщения! (код приходит в течение 4-5
                минут)</p>

            <div class="flex justify-center gap-3 mb-4" id="pin-container">
                @for ($i = 0; $i < 4; $i++)
                    <input type="tel" maxlength="1"
                           class="pin-input w-12 h-12 text-center border border-gray-300 rounded text-xl focus:outline-none focus:border-neutral focus:ring-2 focus:ring-neutral"/>
                @endfor
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-neutral" id="submitEdit">Подтвердить</button>
                    <button class="btn" id="cancelBtn">Отмена</button>
                </form>
            </div>
        </div>
    </dialog>
@endsection

@section('scripts')
    @vite('resources/js/profile.js')
@endsection

