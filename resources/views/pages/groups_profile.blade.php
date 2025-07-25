@extends('layout')
@section('title', 'Мои группы')

@section('style')

@endsection

@section('content')
    <x-profile-menu>
        <div class="card shadow-md p-5 rounded-lg mb-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                </svg>
                <div class="text-2xl font-bold select-none">Занятия</div>
            </div>

            <form action="{{route('profileGroups')}}" method="get">
                <div class="sort-block mb-3 px-2 flex justify-between items-center">
                    <div class="w-full">
                        <label class="floating-label">
                            <input type="text" placeholder="Поиск" class="input input-sm" name="search"
                                   value="{{request('search')}}"/>
                            <span>Поиск</span>
                        </label>
                    </div>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost rounded-full m-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                            </svg>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li>
                                <a href="?sort_field=pivot_created_at&sort_direction=desc&search={{ request('search') }}"
                                   class="sort-option">
                                    Записанные недавно
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=pivot_created_at&sort_direction=asc&search={{ request('search') }}"
                                   class="sort-option">
                                    Записанные давно
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=created_at&sort_direction=desc&search={{ request('search') }}"
                                   class="sort-option">
                                    Новые группы
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=created_at&sort_direction=asc&search={{ request('search') }}"
                                   class="sort-option">
                                    Старые группы
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=title&sort_direction=asc&search={{ request('search') }}"
                                   class="sort-option">
                                    По алфавиту А-Я
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=title&sort_direction=desc&search={{ request('search') }}"
                                   class="sort-option">
                                    По алфавиту Я-А
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>

            <ul class="list">
                @forelse($groups as $group)
                    <li class="list-row flex flex-col sm:flex-row items-center sm:justify-between gap-4 p-2 w-full mb-4 sm:mb-0">
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                            <div class="w-full sm:w-32 sm:h-32 rounded-box overflow-hidden flex-shrink-0">
                                <img class="w-full h-auto sm:w-32 sm:h-32 object-cover"
                                     src="{{$group->preview ?? $group->video_preview}}" alt="{{$group->title}}"/>
                            </div>
                            <div class="flex flex-col text-center sm:text-left w-full">
                                <div class="font-medium text-lg">{{$group->title}}
                                </div>
                                @php
                                    $parsedDateTime = \Carbon\Carbon::parse($group->date . ' ' . $group->time);
                                @endphp

                                <div class="text-sm font-semibold opacity-60 mt-2 sm:mt-0">
                                    Время: {{ $parsedDateTime->format('H:i') }}</div>
                                <div class="text-sm font-semibold opacity-60 mt-2 sm:mt-0">
                                    Дата: {{ $parsedDateTime->translatedFormat('d F Y') }}</div>
                                @php
                                    $schedule = json_decode($group->schedule, true);
                                    $activeDays = collect($schedule)
                                        ->filter(fn($v) => $v)
                                        ->keys()
                                        ->implode(', ');
                                @endphp
                                @if($activeDays)
                                    <div class="text-sm font-semibold opacity-60 mt-2 sm:mt-0">
                                        Расписание: {{ $activeDays }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">

                            @if($group->pivot->status_confirm == 0)
                                <div class="tooltip tooltip-bottom sm:tooltip-top">
                                    <div class="tooltip-content">
                                        <div class="text-base">Запись не подтверждена.</div>
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
                                <a href="{{route('group', $group->id)}}" class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Чат">
                                <a href="{{route('chat', ['user'=> $group->user->id])}}"
                                   class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                    </svg>

                                </a>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Отменить">
                                <button class="btn btn-square rounded-full del-reg" data-id="{{$group->id}}"
                                        data-desc="{{ $group->title }}">
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
                        Занятия отсутствуют
                    </div>
                @endforelse
                @if($groups->hasPages())
                    <div class="mt-3">
                        {{ $groups->links() }}
                    </div>
                @endif
            </ul>

        </div>
    </x-profile-menu>
@endsection

@section('block')
    <dialog id="cancelReg" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Отмена записи</h3>
            <p class="py-4">
                Вы уверены что хотите отменить запись в наборе:
                <span id="groupNameSpan" class="text-red-600 font-bold"></span>?
            </p>
            <div class="modal-action">
                <form method="POST" action="{{route('groupUserDelete')}}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="group_id" id="cancelGroupId">
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
        const cancelGroupId = document.getElementById('cancelGroupId');
        const cancelForm = document.getElementById('cancelForm');

        document.querySelectorAll('.del-reg').forEach(button => {
            button.addEventListener('click', (event) => {
                const btn = event.currentTarget;
                const groupId = btn.dataset.id;
                const groupDesc = btn.dataset.desc;

                groupNameSpan.textContent = groupDesc;
                cancelGroupId.value = groupId;

                modalCancel.showModal();
            });
        });

        document.getElementById('cancelClose').addEventListener('click', () => {
            modalCancel.close();
        });

    </script>
@endsection
