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
                          d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                </svg>
                <div class="text-2xl font-bold select-none">Наборы</div>

                <button class="btn btn-neutral ml-auto btn-sm" onclick="create_group.showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Добавить
                </button>
            </div>
            <form action="{{route('profileMyGroups')}}" method="get">
                <div class="sort-block mb-3 px-2 flex justify-between items-center">
                    <div class="w-full">
                        <label class="floating-label">
                            <input type="text" placeholder="Поиск" id="search" name="search"
                                   value="{{request('search')}}"
                                   class="input input-sm"/>
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
                                <img class="preview_group w-full h-auto sm:w-32 sm:h-32 object-cover"
                                     src="{{$group->preview ?? $group->video_preview}}"/>
                            </div>
                            <div class="title_group flex flex-col text-center sm:text-left w-full">
                                <div class="font-medium text-lg">{{$group->title}}
                                </div>
                                <div
                                    class="description_group text-sm font-semibold opacity-60 mt-2 sm:mt-0 truncate w-64 sm:w-96 mx-auto sm:mx-0">
                                    {{ $group->description }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Список пользователей">
                                <button class="btn btn-square rounded-full listUsersBtn" data-group-id="{{$group->id}}"
                                        data-group-name="{{$group->title}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Страница группы">
                                <a href="{{route('group', $group->id)}}" class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Редактировать">
                                <a href="{{ route('groupEdit', ['group' => $group->id]) }}"
                                   class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Удалить">
                                <button class="btn btn-square rounded-full"
                                        onclick="confirmDelete({{ $group->id }}, '{{ addslashes($group->title) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
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
            </ul>

        </div>

        {{$groups->links()}}

    </x-profile-menu>
@endsection

@section('block')
    <dialog id="delete_modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Удалить группу?</h3>
            <p class="py-2">
                Вы уверены, что хотите удалить группу <span class="font-semibold text-red-500" id="group_title"></span>?
                <br>
                Это действие необратимо.
            </p>
            <form method="POST" id="delete_form">
                @csrf
                @method('DELETE')
                <div class="modal-action">
                    <button type="button" class="btn" onclick="delete_modal.close()">Отмена</button>
                    <button type="submit" class="btn btn-error">Удалить</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="videoPreviewModal" class="modal">
        <div class="modal-box py-6 max-w-xl">
            <h3 class="text-lg font-bold">Предпросмотр видео</h3>
            <video id="videoPreview" class="mt-4 w-full rounded-lg" controls style="display: none;"></video>
            <div class="flex justify-end mt-4">
                <button id="closeVideoPreview" class="btn btn-neutral">Закрыть</button>
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

    <x-create-group/>
@endsection

@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>


    <script>
        function confirmDelete(groupId, groupTitle) {
            const form = document.getElementById('delete_form');
            const titleSpan = document.getElementById('group_title');

            form.action = `{{ route('deleteGroup', ['group' => '__ID__']) }}`.replace('__ID__', groupId);
            titleSpan.textContent = `"${groupTitle}"`;

            delete_modal.showModal();
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get("modal") === "open") {
                create_group.showModal();
            }
        });
    </script>

    <script>
        document.getElementById('create-form').addEventListener('submit', function () {
            const loadingText = document.getElementById('loading-post');
            loadingText.innerHTML = 'Идёт загрузка поста, пожалуйста подождите...';
            loadingText.classList.remove('hidden');

            create_group.close();
        });
    </script>

    <script>
        const videoInput = document.getElementById('videoInput');
        const videoPreview = document.getElementById('videoPreview');
        const videoPreviewModal = document.getElementById('videoPreviewModal');
        const closeVideoPreview = document.getElementById('closeVideoPreview');
        const buttonPreview = document.getElementById('button-preview');

        videoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('video/')) {
                const url = URL.createObjectURL(file);
                videoPreview.src = url;
                videoPreview.style.display = 'block';

                buttonPreview.style.display = 'inline-block';
            } else {
                buttonPreview.style.display = 'none';
            }
        });

        buttonPreview.addEventListener('click', function () {
            videoPreviewModal.showModal();
        });

        closeVideoPreview.addEventListener('click', function () {
            videoPreviewModal.close();
            videoPreview.style.display = 'none';
            videoPreview.src = '';
        });
    </script>


    @vite(['resources/js/myGroups.js', 'resources/js/listUsersFetch.js'])
@endsection
