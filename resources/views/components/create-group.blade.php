<dialog id="create_group" class="modal">
    <div class="modal-box py-6 max-w-xl">

        <h3 class="text-lg font-bold">Создание группы</h3>

        <form id="create-form" action="{{route('groupCreate')}}" method="POST" enctype="multipart/form-data">

            @csrf
            <x-elements.input label="Название" name="title" id="title" value="{{old('title')}}" type="text"
                              placeholder="Название группы" isRequired="true"/>

            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">Описание <span class="text-red-400 mr-5">*</span></legend>
                <textarea class="textarea h-32 w-full" name="description" required
                          placeholder="Описание группы">{{old('description')}}</textarea>
            </fieldset>

            <fieldset class="fieldset ">
                <legend class="fieldset-legend text-base">Количество людей <span class="text-red-400 mr-5">*</span>
                </legend>
                <input id="people-range" type="range" min="0" max="99" value="{{old('count_people') ?? 0}}"
                       class="range range-sm w-full"/>

                <input id="people-input" min="0" max="99" value="{{old('count_people') ?? 0}}" name="count_people"
                       class="w-16 text-center border-0 border-b-2 border-gray-400 focus:outline-none focus:border-gray-800 bg-transparent"/>
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
                    Выберите до 5 направлений
                </span>

                <input type="hidden" name="directions" id="selected-directions" value="{{ old('directions') }}">
            </fieldset>

            <fieldset class="fieldset my-2">
                <legend class="fieldset-legend text-base">
                    Ограничения <span class="text-red-400 mr-5">*</span>
                </legend>

                <label class="option text-sm flex items-center gap-1">
                    <input type="checkbox" name="isAdult" class="checkbox checkbox-sm"/>
                    18+
                </label>
            </fieldset>

            <fieldset class="fieldset my-2">
                <legend class="fieldset-legend text-base">Уровень <span class="text-red-400 mr-5">*</span></legend>

                <div class="flex flex-wrap gap-3">
                    <label class="option text-sm flex items-center gap-1">
                        <input type="checkbox" name="levels[]" value="beginner" class="checkbox checkbox-sm"
                               @if(in_array('beginner', old('levels', []))) checked @endif/>
                        С нуля
                    </label>

                    <label class="option text-sm flex items-center gap-1">
                        <input type="checkbox" name="levels[]" value="starter" class="checkbox checkbox-sm"
                               @if(in_array('starter', old('levels', []))) checked @endif/>
                        Начинающий
                    </label>

                    <label class="option text-sm flex items-center gap-1">
                        <input type="checkbox" name="levels[]" value="intermediate" class="checkbox checkbox-sm"
                               @if(in_array('intermediate', old('levels', []))) checked @endif/>
                        Средний
                    </label>

                    <label class="option text-sm flex items-center gap-1">
                        <input type="checkbox" name="levels[]" value="advanced" class="checkbox checkbox-sm"
                               @if(in_array('advanced', old('levels', []))) checked @endif/>
                        Продолжающий
                    </label>
                </div>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">Категория набора <span class="text-red-400 mr-5">*</span>
                </legend>
                <select class="select w-full" id="category-select" name="class">
                    <option disabled {{ old('class') ? '' : 'selected' }}>Выберите категорию</option>
                    <option value="regular_group" data-schedule="true"
                            {{ old('class') == 'regular_group' ? 'selected' : '' }}
                            data-desc="Группа с постоянным расписанием, в которую можно присоединиться в любой момент">
                        Регулярная группа
                    </option>
                    <option value="course" data-schedule="true"
                            {{ old('class') == 'course' ? 'selected' : '' }}
                            data-desc="Группа, в которой все начинают в один день, нельзя присоединиться после старта">
                        Курс
                    </option>
                    <option value="intensive" data-schedule="true"
                            {{ old('class') == 'intensive' ? 'selected' : '' }}
                            data-desc="Несколько занятий в течение короткого срока (обычно 1-3 дня)">
                        Интенсив
                    </option>
                    <option value="class" data-schedule="false"
                            {{ old('class') == 'class' ? 'selected' : '' }}
                            data-desc="Разовое занятие">Класс
                    </option>
                    <option value="private_lesson" data-schedule="false" data-not-required="true"
                            {{ old('class') == 'private_lesson' ? 'selected' : '' }}
                            data-desc="Занятие один на один с преподавателем, даты и локация обговариваются лично">
                        Индивидуальное занятие
                    </option>
                    <option value="guest_masterclass" data-schedule="false"
                            {{ old('class') == 'guest_masterclass' ? 'selected' : '' }}
                            data-desc="Класс или интенсив с привозным преподавателем-экспертом">Привозной мастер-класс
                    </option>
                </select>
                <span id="desc-category" class="fieldset-label text-sm"></span>
            </fieldset>

            @php
                $now = \Carbon\Carbon::now()->format('Y-m-d');
            @endphp

            <x-elements.input label="Дата старта" name="date" id="date_start" min="{{$now}}"
                              type="date" value="{{ old('date', '') }}"/>

            <x-elements.input label="Дата окончания" name="date_end" id="date_end" min="{{$now}}"
                              type="date" value="{{ old('date_end', '') }}"/>

            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Время начала занятия
                </legend>

                <select id="time" class="input pika-single w-full pr-10" name="time">
                    <option value="" disabled {{ old('time') ? '' : 'selected' }}>Выберите время</option>
                    @for($hour = 0; $hour <= 23; $hour++)
                        @for($min = 0; $min < 60; $min += 30)
                            @php
                                $h = str_pad($hour, 2, '0', STR_PAD_LEFT);
                                $m = str_pad($min, 2, '0', STR_PAD_LEFT);
                                $timeValue = "$h:$m";
                            @endphp
                            <option value="{{ $timeValue }}" {{ old('time') === $timeValue ? 'selected' : '' }}>
                                {{ $timeValue }}
                            </option>
                        @endfor
                    @endfor
                </select>
            </fieldset>

            <fieldset class="fieldset my-2" id="schedule" style="display: none">
                <legend class="fieldset-legend text-base">Расписание группы</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach (['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $day)
                        <label class="cursor-pointer flex items-center justify-center">
                            <input type="checkbox" id="selected_week_{{ $day }}" name="selected_week[]"
                                   value="{{ $day }}" class="hidden peer"
                                {{ in_array($day, old('selected_week', [])) ? 'checked' : '' }}/>
                            <span
                                class="btn btn-neutral btn-sm font-bold py-2 px-4 rounded inline-block peer-checked:bg-[#00EFEE] peer-checked:border-[#00EFEE] peer-checked:text-black">
                                {{ $day }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <x-elements.input label="Цена (₽)" name="price" min="0" id="price" value="{{old('price')}}" type="number"
                              placeholder="Стоимость вступления в группу"/>

            <x-elements.input label="Продолжительность занятия (в минутах)" name="duration" min="0" id="duration"
                              value="{{old('duration')}}" type="number"
                              placeholder="Продолжительность занятия (в минутах)"/>

            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">Адрес</legend>
                <select class="select w-full" name="address" id="address">
                    <option disabled @if(old('address') === null) selected @endif>Выберите адрес</option>
                    @forelse($addresses as $address)
                        <option
                            value="{{$address->id}}"
                            @if(old('address') == $address->id) selected @endif
                        >
                            {{$address->studio_name . ' — ' . $address->studio_address}}
                        </option>
                    @empty
                        <option>Адреса отсутствуют</option>
                    @endforelse
                </select>
            </fieldset>


            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">Фото группы</legend>
                <input type="file" name="preview" class="file-input w-full"/>
                <label class="fieldset-label">Максимальный размер файла: 4мб</label>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">Видео группы <span class="text-red-400 mr-5">*</span></legend>
                <input type="file" name="video_group" required class="file-input w-full" id="videoInput"/>
                <label class="fieldset-label">Максимальный размер файла: 70мб</label>
            </fieldset>

            <button id="button-preview" type="button" class="btn btn-outline mt-2" style="display: none;">Предпросмотр
                видео
            </button>

            <input name="is_schedule" value="false" type="hidden" id="is_schedule">

            <button type="submit" id="submit_data" form="create-form" class="btn btn-neutral mt-3 mb-3 w-full">
                <span class="loading loading-spinner" id="loadingSpinner" style="display: none;"></span>
                Создать
            </button>
        </form>

        <form method="dialog" class="modal-backdrop mb-8">
            <button class="btn">Отмена</button>
        </form>
    </div>

</dialog>

