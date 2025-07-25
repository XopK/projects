@extends('layout')
@section('title', 'Преподаватели')

@section('style')
    <style>
        .fade-out {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 300ms ease, transform 300ms ease;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-10 my-5">
        <div class="filter-block">
            <label class="floating-label block w-full mb-5">
                <input type="text" id="searchInput" placeholder="Поиск" class="input input-lg w-full"/>
                <span>Поиск</span>
            </label>
            <div class="shadow-md p-5 w-full flex flex-col sm:flex-row items-center rounded-lg gap-3 justify-between">
                <!-- Селект с шириной, которая будет больше на широких экранах -->
                <div class="w-full sm:w-1/4">
                    <div class="dropdown dropdown-bottom w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Направления</legend>
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
                            <legend class="fieldset-legend">Сложность</legend>
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
                            <legend class="fieldset-legend">Категории</legend>
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

                <div class="w-full sm:w-1/4">
                    <div class="dropdown dropdown-bottom w-full">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Адрес</legend>
                            <div tabindex="0" class="select w-full">Адрес</div>
                        </fieldset>
                        <ul tabindex="0"
                            class="dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-full max-h-60 overflow-y-auto divide-y divide-gray-200">
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


                <div class="dropdown dropdown-center mt-0 lg:mt-8 md:mt-7 sm:mt-6">
                    <div tabindex="0" role="button" class="btn btn-ghost rounded-full m-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/>
                        </svg>
                    </div>
                    <ul tabindex="0" id="sortDropdown"
                        class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                        <li><a data-sort="asc" data-field="name">По алфавиту А-Я</a></li>
                        <li><a data-sort="desc" data-field="name">По алфавиту Я-А</a></li>
                        <li><a data-sort="asc" data-field="count">По числу наборов (⬆️)</a></li>
                        <li><a data-sort="desc" data-field="count">По числу наборов (⬇️)</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="teacher-block mt-5">
            <div id="teacher-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            </div>
        </div>

        <div id="scrollLoader" class="h-10 my-4 w-full flex justify-center items-center text-gray-500">
            <span class="loading loading-dots loading-xl"></span>
        </div>


    </div>
@endsection

@section('scripts')
    @vite('resources/js/teachersFetch.js')
@endsection
