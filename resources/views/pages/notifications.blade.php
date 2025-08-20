@extends('layout')
@section('title', 'Личный профиль')

@section('style')

@endsection

@section('content')
    <x-profile-menu>
        <!-- Изменить данные -->
        <div class="card shadow-md p-5 rounded-lg mb-5">
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="notify-title-up flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                    <div class="text-2xl font-bold select-none">Уведомления</div>
                </div>
                <a href="{{route('profileNotificationsAllRead')}}" class="btn btn-neutral btn-sm">Прочитать все</a>
            </div>
            <div class="notify-block space-y-4">
                <ul class="list">
                    @forelse($unreadNotifications as $notification)

                        <li class="list-row flex flex-col sm:flex-row items-center sm:justify-between gap-4 p-2 w-full mb-4 sm:mb-0">
                            <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                                <div
                                    class="w-full sm:w-32 sm:h-32 rounded-box overflow-hidden flex items-center justify-center text-gray-400 text-4xl">
                                    🔔
                                </div>

                                <div class="flex flex-col text-center sm:text-left w-full">
                                    <div class="font-medium text-lg">
                                        {{ $notification->data['title'] ?? 'Уведомление' }}
                                    </div>
                                    <div
                                        class="description_group text-sm font-semibold opacity-60 mt-2 sm:mt-0 w-64 sm:w-96 mx-auto sm:mx-0">
                                        {{ $notification->data['message'] ?? 'Нет текста' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if(!empty($notification->data['link']))
                                    <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Перейти">
                                        <a href="{{ route('markRead', ['idNotify' => $notification->id]) }}"
                                           class="btn btn-square rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </li>

                    @empty
                        <div class="w-full text-center text-lg text-gray-500 py-5">
                            Нет новых уведомлений.
                        </div>
                    @endforelse
                </ul>

            </div>
        </div>

        <div class="card shadow-md p-5 rounded-lg mb-5">
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="notify-title-up flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                    </svg>
                    <div class="text-2xl font-bold select-none">Прочитанные уведомления</div>
                </div>
            </div>
            <div class="notify-block space-y-4">
                <ul class="list">
                    @forelse($readNotifications as $notification)
                        <li class="list-row flex flex-col sm:flex-row items-center sm:justify-between gap-4 p-2 w-full mb-4 sm:mb-0 opacity-60">
                            <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                                <div class="w-full sm:w-32 sm:h-32 rounded-box overflow-hidden flex items-center justify-center text-gray-400 text-4xl">
                                    🔔
                                </div>

                                <div class="flex flex-col text-center sm:text-left w-full">
                                    <div class="font-medium text-lg">
                                        {{ $notification->data['title'] ?? 'Уведомление' }}
                                    </div>
                                    <div class="description_group text-sm font-semibold opacity-60 mt-2 sm:mt-0 w-64 sm:w-96 mx-auto sm:mx-0">
                                        {{ $notification->data['message'] ?? 'Нет текста' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(!empty($notification->data['link']))
                                        <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Перейти">
                                            <a href="{{$notification->data['link']}}"
                                               class="btn btn-square rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <div class="w-full text-center text-lg text-gray-500 py-5">
                            Нет прочитанных уведомлений.
                        </div>
                    @endforelse
                </ul>
            </div>
        </div>


    </x-profile-menu>
@endsection

