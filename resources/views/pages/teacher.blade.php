@extends('layout')
@section('title', $teacher->name . ' ' . $teacher->nickname)

@section('meta')
    <meta property="og:title" content="{{$teacher->name . ' ' . $teacher->nickname}}"/>
    <meta property="og:image" content="{{$teacher->photo_profile}}"/>
@endsection

@section('style')
    <style>
        .fade-out {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-10 my-5">
        <!-- Информация о преподавателе -->
        <div class="info-teacher relative bg-white shadow-lg rounded-lg p-6 flex flex-col items-center">
            <!-- Фон за аватаркой -->
            <div
                class="w-full h-[190px] absolute top-0 left-0 rounded-t-lg bg-cover bg-center"
                style="{{ $teacher->descTeacher->photo_teacher ? 'background-image: url(' . asset($teacher->descTeacher->photo_teacher) . ');': 'background-color: ' . $teacher->descTeacher->bg_color . ';' }}">
            </div>

            @if(auth()->check() && auth()->user()->id === $teacher->id)
                <div class="absolute top-4 right-4 flex items-center gap-2">

                    @if($teacher->descTeacher->photo_teacher)
                        <button class="btn btn-circle btn-error" onclick="deletePhoto.showModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif

                    <button class="btn btn-circle btn-warning"
                            onclick="editTeacher.showModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                        </svg>
                    </button>
                </div>
            @endif

            <figure
                class="w-40 h-40 rounded-full overflow-hidden border-4 border-white bg-white relative mt-10">
                <img src="{{$teacher->photo_profile}}"
                     alt="{{$teacher->name}}" class="w-full h-full object-cover"/>
            </figure>

            <h2 class="text-2xl font-semibold text-center mt-4">{{$teacher->name}} {{$teacher->nickname}}</h2>

            @if($teacher->descTeacher->description)
                <div class="divider w-2xs sm:w-xl mx-auto m-0"></div>
                <p class="text-gray-600 w-auto px-6 sm:px-30 text-center whitespace-pre-wrap">{{$teacher->descTeacher->description}}</p>
                <div class="divider w-2xs sm:w-xl mx-auto m-0"></div>
            @endif

            @if(!empty($teacher->descTeacher->categories) && count($teacher->descTeacher->categories) > 0)
                <div class="mt-2 flex flex-wrap justify-center gap-2">
                    @foreach($teacher->descTeacher->categories as $categoryDesc)
                        <span
                            class="px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm">{{ $categoryDesc->name }}</span>
                    @endforeach
                </div>
            @endif

            @if($teacher->descTeacher->experience)
                <p class="text-gray-600 font-semibold mt-3 w-auto px-3 sm:px-40 text-center">
                    Стаж преподавания: {{$teacher->formated_experience}}
                </p>
            @endif
            <p class="text-gray-500 font-bold mt-2">На
                сайте: {{ \Carbon\Carbon::parse($teacher->created_at)->translatedFormat('с\ d F Y') }}</p>

            <a href="{{route('chat', ['user'=> $teacher->id])}}" class="btn btn-neutral mt-3">Связаться</a>
        </div>


        <!-- Основной контейнер -->
        <div class="mt-8 space-y-6">

            <!-- Блок фильтров -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Фильтры групп</h2>

                <div class="w-full flex flex-col items-center sm:flex-row gap-3">
                    <input type="text" id="search-input" placeholder="Поиск по названию группы..."
                           class="input input-bordered w-full sm:w-1/4"/>

                    <div class="w-full sm:w-1/4">
                        <div class="dropdown dropdown-bottom w-full">
                            <fieldset class="fieldset">
                                <div tabindex="0" class="select w-full">Направления</div>
                            </fieldset>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200">
                                @forelse($categories as $category)
                                    <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="categories[]" value="{{ $category->slug }}"
                                                   class="checkbox"/>
                                            <span>{{ $category->name }}</span>
                                        </label>
                                    </li>
                                @empty
                                    <li>
                                        <span class="text-gray-500">Категории отсутствуют</span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="w-full sm:w-1/4">
                        <div class="dropdown dropdown-bottom w-full">
                            <fieldset class="fieldset">
                                <div tabindex="0" class="select w-full">Сложность</div>
                            </fieldset>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200">
                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="levels[]" value="beginner"
                                               class="checkbox"/>
                                        <span>С нуля</span>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="levels[]" value="starter"
                                               class="checkbox"/>
                                        <span>Начинающий</span>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="levels[]" value="intermediate"
                                               class="checkbox"/>
                                        <span>Средний</span>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="levels[]" value="advanced"
                                               class="checkbox"/>
                                        <span>Продолжающий</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="w-full sm:w-1/4">
                        <div class="dropdown dropdown-bottom w-full">
                            <fieldset class="fieldset">
                                <div tabindex="0" class="select w-full">Категории</div>
                            </fieldset>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200">
                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-start space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="regular_group"
                                               class="checkbox mt-1"/>
                                        <div>
                                            <span class="font-medium">Регулярная группа</span>
                                            <div class="text-gray-500 text-sm">Группа с постоянным расписанием, в
                                                которую можно присоединиться в любой момент
                                            </div>
                                        </div>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="course"
                                               class="checkbox"/>
                                        <div>
                                            <span class="font-medium">Курс</span>
                                            <div class="text-gray-500 text-sm">Группа, в которой все начинают в один
                                                день, нельзя присоединиться после старта
                                            </div>
                                        </div>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="intensive"
                                               class="checkbox"/>
                                        <div>
                                            <span class="font-medium">Интенсив</span>
                                            <div class="text-gray-500 text-sm">Несколько занятий в течение короткого
                                                срока (обычно 1-3 дня)
                                            </div>
                                        </div>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="class"
                                               class="checkbox"/>
                                        <div>
                                            <span class="font-medium">Класс</span>
                                            <div class="text-gray-500 text-sm">
                                                Разовое занятие
                                            </div>
                                        </div>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="private_lesson"
                                               class="checkbox"/>
                                        <div>
                                            <span class="font-medium">Индивидуальное занятие</span>
                                            <div class="text-gray-500 text-sm">
                                                Занятие один на один с преподавателем, даты и локация обговариваются
                                                лично
                                            </div>
                                        </div>
                                    </label>
                                </li>

                                <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="class[]" value="guest_masterclass"
                                               class="checkbox"/>
                                        <div>
                                            <span class="font-medium">Привозной мастер-класс</span>
                                            <div class="text-gray-500 text-sm">
                                                Класс или интенсив с привозным преподавателем-экспертом
                                            </div>
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="dropdown dropdown-end ml-auto">
                        <div tabindex="0" role="button" class="btn btn-ghost rounded-full m-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                            </svg>
                        </div>
                        <ul tabindex="0" id="sortDropdown"
                            class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li><a data-sort="desc" data-field="created_at">Сначала новые</a></li>
                            <li><a data-sort="asc" data-field="created_at">Сначала старые</a></li>
                            <li><a data-sort="asc" data-field="title">По алфавиту А-Я</a></li>
                            <li><a data-sort="desc" data-field="title">По алфавиту Я-А</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Блок карточек групп -->

            <h2 class="text-xl font-semibold mb-4">Группы преподавателя</h2>

            <div class="groups-block">
                <div id="group-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Здесь будут карточки групп -->
                </div>
            </div>

            <div id="scrollLoader" class="h-10 py-50 my-4 w-full flex justify-center items-center text-gray-500">
                <span class="loading loading-dots loading-xl"></span>
            </div>


        </div>
    </div>
@endsection

@section('block')
    @if(auth()->check() && auth()->user()->id === $teacher->id)
        <dialog id="editTeacher" class="modal">
            <form id="postEditTeacher" enctype="multipart/form-data" method="post"
                  action="{{route('updateTeacher', ['teacher' => $teacher->id])}}"
                  class="modal-box">
                @method('PUT')
                @csrf
                <h3 class="text-lg font-bold mb-4">Редактирование</h3>

                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend text-base">Описание</legend>
                    <textarea
                        class="textarea h-32 w-full"
                        name="description"
                        placeholder="Описание">{{$teacher->descTeacher->description ?? ''}}</textarea>
                </fieldset>

                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend text-base">Стаж преподавания
                    </legend>

                    <input min="0" type="number" name="experience" value="{{$teacher->descTeacher->experience}}"
                           class="input input-bordered w-full h-10 cursor-pointer">

                    <p class="label">Указывайте стаж в месяцах.</p>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Направления <span class="text-red-400 mr-5">*</span>
                    </legend>

                    <div class="relative">
                        <div id="badge-container"
                             class="flex flex-wrap items-center gap-2 p-3 border border-gray-300 rounded cursor-pointer min-h-[3rem]">
                        <span class="opacity-50 my-auto" id="placeholder"
                              style="font-size: 13px; user-select: none">Выберите направления</span>
                        </div>

                        <!-- Выпадающий список -->
                        <div id="dropdown"
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded shadow-md hidden">
                            <ul class="max-h-40 overflow-y-auto p-2">
                                @foreach($categories as $category)
                                    <li class="cursor-pointer hover:bg-gray-100 px-2 py-1 rounded"
                                        data-id="{{$category->id}}"
                                        onclick="selectOption('{{$category->name}}', {{$category->id}})">{{$category->name}}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>

                    <span class="fieldset-label">
                        Выберите до 3-х направлений
                    </span>

                    <input type="hidden" name="directions" id="selected-directions"
                           value="{{ $teacher->descTeacher->categories }}">
                </fieldset>

                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend text-base">Цвет фона</legend>

                    <input type="color" name="bg_color" value="{{$teacher->descTeacher->bg_color}}"
                           class="input w-full h-10 cursor-pointer">
                </fieldset>

                <div class="divider mb-2">ИЛИ</div>

                <fieldset class="fieldset mb-3">
                    <legend class="fieldset-legend text-base">Изображение для фона</legend>

                    <input type="file" name="photo_teacher" class="file-input w-full"/>

                    <p class="label">Максимальный размер файла 50 МБ</p>
                </fieldset>

                <div class="modal-action pb-15">
                    <button type="submit" form="postEditTeacher" class="btn btn-neutral">Сохранить</button>
                    <button type="button" class="btn" onclick="document.getElementById('editTeacher').close()">Отмена
                    </button>
                </div>
            </form>
        </dialog>

        <dialog id="deletePhoto" class="modal">
            <form id="postDeletePhoto" method="post"
                  action="{{route('deleteTeacher', ['teacher'=> $teacher->id])}}"
                  class="modal-box">
                @method('DELETE')
                @csrf
                <h3 class="text-lg font-bold mb-4">Вы уверены?</h3>
                <p class="mb-4">Вы действительно хотите удалить фото профиля?</p>
                <div class="modal-action">
                    <button type="submit" form="postDeletePhoto" class="btn btn-error">Удалить</button>
                    <button type="button" class="btn" onclick="document.getElementById('deletePhoto').close()">Отмена
                    </button>
                </div>
            </form>
        </dialog>
    @endif
@endsection

@section('scripts')
    @if(auth()->check() && auth()->user()->id === $teacher->id)
        <script>
            const badgeContainer = document.getElementById('badge-container');
            const dropdown = document.getElementById('dropdown');
            const placeholder = document.getElementById('placeholder');
            const hiddenInput = document.getElementById('selected-directions');

            let selected = [];  // Массив для хранения объектов с названием и ID
            document.addEventListener('DOMContentLoaded', function () {
                badgeContainer.addEventListener('click', toggleDropdown);

                function toggleDropdown() {
                    dropdown.classList.toggle('hidden');
                }

                window.selectOption = function (name, id) {
                    if (selected.some(item => item.id === id)) return;
                    if (selected.length >= 3) return;

                    selected.push({name, id});
                    updateBadges();
                };

                window.removeOption = function (id) {
                    selected = selected.filter(item => item.id !== id);
                    updateBadges();
                };

                function updateBadges() {
                    badgeContainer.innerHTML = '';
                    if (selected.length === 0) {
                        placeholder.style.display = 'inline';
                        badgeContainer.appendChild(placeholder);
                    }

                    selected.forEach(item => {
                        const badge = document.createElement('div');
                        badge.className = 'badge badge-neutral flex items-center gap-1';
                        badge.innerHTML = `${item.name} <button onclick="removeOption(${item.id})" class="ml-1 text-sm">✕</button>`;
                        badgeContainer.appendChild(badge);
                    });

                    hiddenInput.value = JSON.stringify(selected);
                }

                document.addEventListener('click', (e) => {
                    if (!badgeContainer.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                const oldValue = hiddenInput.value;
                if (oldValue) {
                    try {
                        const restored = JSON.parse(oldValue);
                        if (Array.isArray(restored)) {
                            selected = restored;
                            updateBadges();
                        }
                    } catch (e) {
                        console.error('Ошибка восстановления направлений:', e);
                    }
                }

            });
        </script>
    @endif

    <script>
        window.teacherId = @json($teacher->id);
    </script>
    @vite('resources/js/teacherProfileFetch.js')
@endsection
