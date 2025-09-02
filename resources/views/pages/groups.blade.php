@extends('layout')
@section('title', 'Поиск групп')

@section('style')
    <style>
        .pika-single {
            &:is(div) {
                & .is-today {
                    .pika-button {
                        background: oklch(0.392 0.027 259.161);
                        color: var(--color-primary-content);
                    }
                }
            }
        }

        .pika-single {
            margin-inline-end: 0;
        }

        .fade-out {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
    </style>

@endsection

@section('content')
    <div class="container mx-auto px-10 my-5">

        <div class="filter-block">

            <label class="floating-label block w-full mb-5">
                <input type="text" placeholder="Поиск" id="search-input" class="input input-lg w-full"/>
                <span>Поиск</span>
            </label>

            <button id="filterToggle" class="btn btn-neutral w-full sm:hidden mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                </svg>
                <span>Фильтр</span>
            </button>

            <div id="filterPanel" class="shadow-md py-4 px-5 w-full flex flex-col gap-3 rounded-lg hidden sm:flex transition-all duration-300 ease-in-out">

                <div class="w-full flex flex-col sm:flex-row gap-3">

                    <div class="w-full sm:w-1/4">
                        <div class="dropdown dropdown-bottom w-full drop-stat">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Направления</legend>
                                <div class="select drop-tog w-full">Направления</div>
                            </fieldset>
                            <ul
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
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
                        <div class="dropdown dropdown-bottom w-full drop-stat">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Категории</legend>
                                <div class="select w-full drop-tog">Категории</div>
                            </fieldset>
                            <ul
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
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

                    <div class="w-full sm:w-1/4">
                        <div class="dropdown dropdown-bottom w-full drop-stat">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Сложность</legend>
                                <div class="select w-full drop-tog">Сложность</div>
                            </fieldset>
                            <ul
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
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
                        <div class="dropdown dropdown-bottom w-full drop-stat">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Преподаватели</legend>
                                <div class="select w-full drop-tog">Преподаватели</div>
                            </fieldset>
                            <ul
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
                                @forelse($teachers as $teacher)
                                    <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="teachers[]" value="{{ $teacher->id }}"
                                                   class="checkbox"/>
                                            <span>{{ $teacher->name }} {{ $teacher->nickname }}</span>
                                        </label>
                                    </li>
                                @empty
                                    <li class="py-2 px-3 text-gray-500">
                                        Преподаватели отсутствуют
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- Поля даты с уменьшенной шириной -->
                <div class="w-full flex flex-col sm:flex-row gap-3 items-center">
                    <div class="w-full sm:w-1/3">
                        <div class="dropdown dropdown-bottom w-full drop-stat">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Адрес</legend>
                                <div class="select drop-tog w-full">Адрес</div>
                            </fieldset>
                            <ul
                                class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200 select-none">
                                @forelse($addresses as $address)
                                    <li class="py-2 px-3 hover:bg-gray-100 transition-colors duration-150">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="address[]" value="{{ $address->id }}"
                                                   class="checkbox"/>
                                            <span>{{ $address->studio_name . ' — ' . $address->studio_address }}</span>
                                        </label>
                                    </li>
                                @empty
                                    <li class="py-2 px-3 text-gray-500">
                                        Адреса отсутствуют
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <fieldset class="fieldset relative w-full sm:w-1/3">
                        <legend class="fieldset-legend">Дата/Дата старта</legend>
                        <input type="text" class="input pika-single w-full pr-10" placeholder="Дата/Дата старта"
                               id="date"
                               readonly>
                        <button id="clearDate" class="absolute right-2 top-3 w-6 h-6 cursor-pointer hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <svg id="calendarIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor" class="absolute right-2 top-3 w-6 h-6 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
                        </svg>
                    </fieldset>

                    <fieldset class="fieldset relative w-full sm:w-1/3">
                        <legend class="fieldset-legend">Время начала занятия</legend>
                        <select id="time" class="input pika-single w-full pr-10" placeholder="Время начала занятия">
                        </select>
                        <button id="clearTime" class="absolute right-2 top-3 w-6 h-6 cursor-pointer hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <svg id="timeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor" class="absolute right-2 top-3 w-6 h-6 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </fieldset>

                    <div class="dropdown dropdown-end ml-auto mt-0 lg:mt-8 md:mt-7 sm:mt-6">
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
        </div>

        <div class="groups-block mt-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="groups-grid">

            </div>
        </div>

        <div id="scrollLoader" class="h-10 my-4 w-full flex justify-center items-center text-gray-500">
            <span class="loading loading-dots loading-xl"></span>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>

    <script>
        const toggleBtn = document.getElementById('filterToggle');
        const filterPanel = document.getElementById('filterPanel')

        toggleBtn.addEventListener('click', function () {
            filterPanel.classList.toggle('hidden')
        })
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.drop-stat');

            dropdowns.forEach(function (dropdown) {
                const toggle = dropdown.querySelector('.drop-tog');

                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();

                    dropdowns.forEach(function (d) {
                        if (d !== dropdown) d.classList.remove('dropdown-open');
                    });

                    dropdown.classList.toggle('dropdown-open');
                });
            });

            document.addEventListener('click', function (e) {
                dropdowns.forEach(function (dropdown) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('dropdown-open');
                    }
                });
            });
        });
    </script>

    @vite('resources/js/fetchGroups.js')
@endsection
