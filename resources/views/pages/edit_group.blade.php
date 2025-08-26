@extends('layout')
@section('title', 'Редактирование группы')

@section('style')
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
    />
@endsection

@section('content')

    <div class="flex flex-col gap-6 px-4 sm:px-6 lg:px-10 mb-6">
        <div class="w-full max-w-6xl mx-auto bg-base-100 shadow-xl relative rounded-xl p-4 sm:p-6">
            <a href="{{url()->previous()}}" class="btn btn-neutral absolute top-3 sm:top-5 right-3">Назад</a>
            <h2 class="text-xl font-bold mb-4">Форма редактирования</h2>
            <form action="{{route('groupUpdate', ['group' => $group->id])}}" method="POST" id="create-form"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-elements.input label="Название" name="title" id="title" value="{{$group->title}}" type="text"
                                  placeholder="Название группы" isRequired="true"/>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Описание <span class="text-red-400 mr-5">*</span>
                    </legend>
                    <textarea
                            class="textarea w-full resize-none auto-grow"
                            name="description"
                            required
                            placeholder="Описание группы"
                            oninput="autoGrow(this)">{{$group->description}}</textarea>
                </fieldset>

                <fieldset class="fieldset ">
                    <legend class="fieldset-legend text-base">Количество людей <span class="text-red-400 mr-5">*</span>
                    </legend>
                    <input id="people-range" type="range" min="0" max="99" value="{{$group->count_people}}"
                           class="range range-sm w-full"/>

                    <input id="people-input" min="0" max="99" value="{{$group->count_people}}" name="count_people"
                           class="w-16 text-center border-0 border-b-2 border-gray-400 focus:outline-none focus:border-gray-800 bg-transparent"/>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Направления <span class="text-red-400 mr-5">*</span>
                    </legend>

                    <div class="relative">
                        <div id="badge-container"
                             class="flex flex-wrap items-center gap-2 p-3 border border-gray-300 rounded cursor-pointer min-h-[3rem]"
                             onclick="toggleDropdown()">
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
                    @php
                        $selectedDirections = $group->categories->map(fn($cat) => [
                            'id' => $cat->id,
                            'name' => $cat->name,
                        ]);
                    @endphp

                    <input type="hidden" name="directions" id="selected-directions" value='@json($selectedDirections)'>

                </fieldset>

                <fieldset class="fieldset my-2">
                    <legend class="fieldset-legend text-base">
                        Ограничения <span class="text-red-400 mr-5">*</span>
                    </legend>

                    <label class="option text-sm flex items-center gap-1">
                        <input type="checkbox" name="isAdult" class="checkbox checkbox-sm" @checked($group->age_verify == 1)/>
                        18+
                    </label>
                </fieldset>

                <fieldset class="fieldset my-2">
                    <legend class="fieldset-legend text-base">Уровень <span class="text-red-400 mr-5">*</span></legend>

                    <div class="flex flex-wrap gap-3">
                        <label class="option text-sm flex items-center gap-1">
                            <input type="checkbox" name="levels[]" value="beginner" class="checkbox checkbox-sm"
                                   @if(in_array('beginner', $selectedLevels)) checked @endif/>
                            С нуля
                        </label>

                        <label class="option text-sm flex items-center gap-1">
                            <input type="checkbox" name="levels[]" value="starter" class="checkbox checkbox-sm"
                                   @if(in_array('starter', $selectedLevels)) checked @endif/>
                            Начинающий
                        </label>

                        <label class="option text-sm flex items-center gap-1">
                            <input type="checkbox" name="levels[]" value="intermediate" class="checkbox checkbox-sm"
                                   @if(in_array('intermediate', $selectedLevels)) checked @endif/>
                            Средний
                        </label>

                        <label class="option text-sm flex items-center gap-1">
                            <input type="checkbox" name="levels[]" value="advanced" class="checkbox checkbox-sm"
                                   @if(in_array('advanced', $selectedLevels)) checked @endif/>
                            Продолжающий
                        </label>
                    </div>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">Категория набора <span class="text-red-400 mr-5">*</span>
                    </legend>
                    <select class="select w-full" id="category-select" name="class">
                        <option disabled {{ $group->class ? '' : 'selected' }}>Выберите категорию</option>
                        <option value="regular_group" data-schedule="true"
                                {{ $group->class == 'regular_group' ? 'selected' : '' }}
                                data-desc="Группа с постоянным расписанием, в которую можно присоединиться в любой момент">
                            Регулярная группа
                        </option>
                        <option value="course" data-schedule="true"
                                {{ $group->class == 'course' ? 'selected' : '' }}
                                data-desc="Группа, в которой все начинают в один день, нельзя присоединиться после старта">
                            Курс
                        </option>
                        <option value="intensive" data-schedule="true"
                                {{ $group->class == 'intensive' ? 'selected' : '' }}
                                data-desc="Несколько занятий в течение короткого срока (обычно 1-3 дня)">
                            Интенсив
                        </option>
                        <option value="class" data-schedule="false"
                                {{ $group->class == 'class' ? 'selected' : '' }}
                                data-desc="Разовое занятие">Класс
                        </option>
                        <option value="private_lesson" data-schedule="false"
                                {{ $group->class == 'private_lesson' ? 'selected' : '' }}
                                data-desc="Занятие один на один с преподавателем, даты и локация обговариваются лично">
                            Индивидуальное занятие
                        </option>
                        <option value="guest_masterclass" data-schedule="false"
                                {{ $group->class == 'guest_masterclass' ? 'selected' : '' }}
                                data-desc="Класс или интенсив с привозным преподавателем-экспертом">Привозной
                            мастер-класс
                        </option>
                    </select>
                    <span id="desc-category" class="fieldset-label text-sm"></span>
                </fieldset>

                @php
                    $date = $group->date ?? null;
                    $time = $group->time ?? null;
                    $dateEnd = $group->date_end ?? null;

                    $dateTimeValue = old('date');
                    $timeValueOption = old('time');
                    $dateEndValue = old('date_end');

                    if (!$dateTimeValue && $date) {
                        $dateTimeValue = \Carbon\Carbon::parse($date)->format('Y-m-d');
                    }

                    if(!$timeValueOption && $time){
                        $timeValueOption = \Carbon\Carbon::parse($time)->format('H:i');
                    }

                    if (!$dateEndValue && $dateEnd) {
                        $dateEndValue = \Carbon\Carbon::parse($dateEnd)->format('Y-m-d');
                    }

                @endphp

                <x-elements.input label="Дата старта" name="date" id="date_start"
                                  type="date" value="{{ $dateTimeValue }}"/>

                <x-elements.input label="Дата окончания" name="date_end" id="date_end"
                                  type="date" value="{{ $dateEndValue }}"/>

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
                                <option
                                        value="{{ $timeValue }}" {{ $timeValueOption === $timeValue ? 'selected' : '' }}>
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
                                        {{ in_array($day, $selectedWeeks) ? 'checked' : '' }}/>
                                <span
                                        class="btn btn-neutral btn-sm font-bold py-2 px-4 rounded inline-block peer-checked:bg-gray-200 peer-checked:border-gray-200 peer-checked:text-black">
                                {{ $day }}
                            </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <x-elements.input label="Цена (₽)" name="price" min="0" id="price" value="{{$group->price}}"
                                  type="number"
                                  placeholder="Стоимость вступления в группу"/>

                <x-elements.input label="Продолжительность занятия (в минутах)" name="duration" min="0" id="duration"
                                  value="{{$group->duration}}"
                                  type="number"
                                  placeholder="Продолжительность занятия (в минутах)"/>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Адрес
                    </legend>

                    <select class="select w-full" name="address">
                        <option disabled @if(optional($group->address)->id === null) selected @endif>
                            Выберите адрес
                        </option>

                        @forelse($addresses as $address)
                            <option
                                value="{{ $address->id }}"
                                @if(optional($group->address)->id == $address->id) selected @endif
                            >
                                {{ $address->studio_name . ' — ' . $address->studio_address }}
                            </option>
                        @empty
                            <option>Адреса отсутствуют</option>
                        @endforelse
                    </select>
                </fieldset>

                <input name="is_schedule" value="false" type="hidden" id="is_schedule">

                <button type="submit" id="submit_data" form="create-form" class="btn btn-neutral mt-3 w-full">
                    <span class="loading loading-spinner" id="loadingSpinner" style="display: none;"></span>
                    Обновить
                </button>
            </form>
        </div>

        <div class="w-full max-w-6xl mx-auto bg-base-100 shadow-xl rounded-xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <h2 class="text-xl font-bold">Фотографии набора</h2>
                @if(auth()->check() && auth()->id() == $group->user_id)
                    <button onclick="addPhoto.showModal()" class="btn btn-sm btn-neutral z-10">
                        + Добавить фотографии
                    </button>
                @endif
            </div>
            @if($group->preview || $group->photoGroups->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">

                    {{-- Превьюшное изображение --}}
                    @if($group->preview)
                    <div class="relative w-full h-64 rounded-lg overflow-hidden group">
                        @if(auth()->check() && auth()->id() == $group->user_id)
                            <form action="{{route('groupDeletePhoto', ['group' => $group->id])}}"
                                  method="POST" class="absolute top-2 right-2 z-10">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" value="1" name="isPreview">
                                <button type="submit" class="btn btn-sm btn-error btn-circle">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                        <a data-fancybox="gallery" data-src="{{$group->preview}}">
                            <img src="{{$group->preview}}" alt="{{$group->title}}"
                                 class="w-full h-full object-cover shadow-lg">
                        </a>
                    </div>
                    @endif

                    {{-- Остальные фотографии --}}
                    @foreach($group->photoGroups as $photo)
                        <div class="relative w-full h-64 rounded-lg overflow-hidden group">
                            @if(auth()->check() && auth()->id() == $group->user_id)
                                <div class="flex items-center gap-2 absolute top-2 right-2 z-10">
                                    <form action="{{route('groupUpdatePreview', ['group' => $group->id])}}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="photo" value="{{$photo->id}}">
                                        <button type="submit" class="btn btn-sm btn-info btn-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </button>
                                    </form>

                                    <form action="{{route('groupDeletePhoto', ['group' => $group->id])}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" value="{{$photo->id}}" name="photo">
                                        <input type="hidden" value="0" name="isPreview">
                                        <button type="submit" class="btn btn-sm btn-error btn-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M6 18 18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                            <a data-fancybox="gallery" data-src="{{$photo->photo}}">
                                <img src="{{$photo->photo}}" alt="{{$group->title}}"
                                     class="w-full h-full object-cover shadow-lg">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>


        <div class="w-full max-w-6xl mx-auto bg-base-100 shadow-xl rounded-xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <h2 class="text-xl font-bold ">Видео набора</h2>
                @if(auth()->check() && auth()->id() == $group->user_id)
                    <button onclick="addVideo.showModal()"
                            class="btn btn-sm btn-neutral z-10">
                        + Добавить видео
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div class="relative w-full rounded-lg overflow-hidden group">
                    @if(auth()->check() && auth()->id() == $group->user_id)
                        <form action="{{route('groupDeleteVideo', ['group' => $group->id])}}"
                              method="POST" class="absolute top-2 right-2 z-10">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" value="1" name="isPreview">
                            <button type="submit" class="btn btn-sm btn-error btn-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                    <video class="plyr" controls playsinline poster="{{$group->video_preview}}">
                        <source src="{{$group->video_group}}" type="video/mp4"/>
                    </video>
                </div>

                @foreach($group->videoGroups as $video)
                    <div class="relative w-full rounded-lg overflow-hidden group">
                        @if(auth()->check() && auth()->id() == $group->user_id)
                            <div class="flex items-center gap-2 absolute top-2 right-2 z-10">
                                <form action="{{route('groupUpdatePreviewVideo', ['group' => $group->id])}}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="video" value="{{$video->id}}">
                                    <button type="submit" class="btn btn-sm btn-info btn-circle">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </button>
                                </form>

                                <form action="{{route('groupDeleteVideo', ['group' => $group->id])}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" value="{{$video->id}}" name="video">
                                    <input type="hidden" value="0" name="isPreview">
                                    <button type="submit" class="btn btn-sm btn-error btn-circle">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                        <video class="plyr" controls playsinline poster="{{$video->preview}}">
                            <source src="{{$group->video}}" type="video/mp4"/>
                        </video>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('block')
    @if(auth()->check() && auth()->id() == $group->user_id)
        <dialog id="addPhoto" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-4">Добавление фотографии</h3>

                <div class="flex items-center justify-center w-full mb-6">
                    <label id="drop-area" for="photo-upload"
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-100 hover:bg-gray-200 transition-all">
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 15a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4M12 3v12m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            <p class="text-sm text-gray-600"><span
                                        class="font-semibold">Нажмите или перетащите файл</span>
                            </p>
                            <p class="text-xs text-gray-400">JPEG, JPG, PNG до 5MB</p>
                        </div>
                        <form action="{{route('groupAddPhoto', ['group' => $group->id])}}" id="postPhoto" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <input id="photo-upload" type="file" multiple class="hidden" name="photos[]"
                                   accept="image/*"/>
                        </form>
                    </label>

                </div>

                <div id="file-preview" class="px-2 text-sm text-gray-700 space-y-1 mt-2"></div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Отмена</button>
                        <button form="postPhoto" type="submit" class="btn btn-neutral">Добавить</button>
                    </form>
                </div>
            </div>
        </dialog>
    @endif

    @if(auth()->check() && auth()->id() == $group->user_id)
        <dialog id="addVideo" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-4">Добавление видео</h3>

                <div class="flex items-center justify-center w-full mb-6">
                    <label id="drop-area-video" for="video-upload"
                           class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-100 hover:bg-gray-200 transition-all">
                        <div class="flex flex-col items-center justify-center py-6 px-4">
                            <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 15a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4M12 3v12m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            <p class="text-sm text-gray-600"><span
                                        class="font-semibold">Нажмите или перетащите файл</span>
                            </p>
                            <p class="text-xs text-gray-400">MP4, WEBM, MOV</p>
                            <p class="text-xs text-gray-400 mt-1">Максимальный размер файла: <span
                                        class="font-semibold">70 МБ</span></p>
                        </div>
                        <form action="{{route('groupAddVideo', ['group' => $group->id])}}" id="postVideo" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <input id="video-upload" type="file" accept="video/*" multiple class="hidden"
                                   name="videos[]"/>
                        </form>
                    </label>

                </div>

                <div id="file-preview-video" class="px-2 text-sm text-gray-700 space-y-1 mt-2"></div>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Отмена</button>
                        <button form="postVideo" type="submit" class="btn btn-neutral">Добавить</button>
                    </form>
                </div>
            </div>
        </dialog>
    @endif
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <script>
        Fancybox.bind('[data-fancybox]', {});
    </script>

    <script>
        document.getElementById('create-form').addEventListener('submit', function () {
            const loadingText = document.getElementById('loading-post');
            loadingText.innerHTML = 'Идёт обновление поста, пожалуйста подождите...';
            loadingText.classList.remove('hidden');

        });
    </script>

    <script>
        function autoGrow(element) {
            element.style.height = 'auto';
            element.style.height = element.scrollHeight + 'px';
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.auto-grow').forEach(textarea => autoGrow(textarea));
        });
    </script>

    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('photo-upload');
        const preview = document.getElementById('file-preview');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.classList.add('bg-gray-200', 'border-gray-400');
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.classList.remove('bg-gray-200', 'border-gray-400');
        });

        function updatePreview(files) {
            preview.innerHTML = '';
            Array.from(files).forEach(file => {
                const item = document.createElement('div');
                item.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                preview.appendChild(item);
            });
        }

        dropArea.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updatePreview(files);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                updatePreview(fileInput.files);
            }
        });
    </script>

    <script>
        const dropAreaVideo = document.getElementById('drop-area-video');
        const fileInputVideo = document.getElementById('video-upload');
        const previewVideo = document.getElementById('file-preview-video');
        const MAX_SIZE = 70 * 1024 * 1024;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropAreaVideo.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropAreaVideo.classList.add('bg-gray-200', 'border-gray-400');
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropAreaVideo.classList.remove('bg-gray-200', 'border-gray-400');
        });

        function updatePreviewVideo(files) {
            previewVideo.innerHTML = '';
            Array.from(files).forEach(file => {
                const item = document.createElement('div');
                item.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                previewVideo.appendChild(item);
            });
        }

        function filterFilesBySize(files) {
            for (const file of files) {
                if (file.size > MAX_SIZE) {
                    addVideo.close();
                    showAlert('Максимальный размер файла 70МБ', 'error');
                    return false;
                }
            }
            return true;
        }

        dropAreaVideo.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                if (filterFilesBySize(files)) {
                    fileInputVideo.files = files;
                    updatePreviewVideo(files);
                } else {
                    fileInputVideo.value = '';
                    previewVideo.innerHTML = '';
                }
            }
        });

        fileInputVideo.addEventListener('change', () => {
            if (fileInputVideo.files.length > 0) {
                if (filterFilesBySize(fileInputVideo.files)) {
                    updatePreviewVideo(fileInputVideo.files);
                } else {
                    fileInputVideo.value = '';
                    previewVideo.innerHTML = '';
                }
            }
        });
    </script>

    <script>
        const addPhotoDialog = document.getElementById('addPhoto');
        const addVideoDialog = document.getElementById('addVideo');

        // После отправки формы "Добавить фото"
        document.getElementById('postPhoto').addEventListener('submit', function () {
            const loadingText = document.getElementById('loading-post');
            loadingText.innerHTML = 'Идёт загрузка фотографий, пожалуйста подождите...';
            loadingText.classList.remove('hidden');

            if (addPhotoDialog?.close) addPhotoDialog.close();
        });

        // После отправки формы "Добавить видео"
        document.getElementById('postVideo').addEventListener('submit', function () {
            const loadingText = document.getElementById('loading-post');
            loadingText.innerHTML = 'Идёт загрузка видео, пожалуйста подождите...';
            loadingText.classList.remove('hidden');

            if (addVideoDialog?.close) addVideoDialog.close();
        });
    </script>

    @vite('resources/js/myGroups.js')
@endsection
