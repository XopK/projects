@extends('layout')
@section('title', 'Чат')

@section('style')
    <style>
        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        .fade-out {
            animation: fadeOut 0.5s forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s forwards;
        }

    </style>
@endsection

@section('content')
    <div class="container mx-auto px-5 xl:px-8 lg:px-10 h-[80vh] my-5">
        <div class="messenger-container h-full flex flex-col md:flex-row">
            <!-- Кнопка для открытия списка диалогов в мобильной версии -->
            <div class="md:hidden">
                <div class="drawer z-9998">
                    <input id="my-drawer-4" type="checkbox" class="drawer-toggle"/>
                    <div class="drawer-side">
                        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
                        <ul id="chat-list-mobile" class="bg-base-200 text-base-content min-h-full w-80 p-4">
                            <h3 class="text-lg font-semibold mb-4">Диалоги</h3>
                        </ul>
                    </div>
                </div>
                <div class="drawer-content z-9997 mb-3">
                    <label for="my-drawer-4" class="drawer-button btn btn-neutral btn-block">Выбрать диалог</label>
                </div>
            </div>

            <!-- Список диалогов (скрывается в мобильной версии) -->
            <div class="hidden md:block w-1/4 bg-gray-100 rounded-lg shadow-lg p-4 overflow-y-auto h-full">
                <h3 class="text-lg font-semibold mb-4">Диалоги</h3>
                <ul id="chat-list">
                </ul>
            </div>

            <!-- Основное окно чата -->
            <div id="chat-container"
                 class="chat-container flex-1 bg-white p-6 rounded-lg shadow-lg flex flex-col h-full md:ml-4 relative">

                <div id="loading-screen"
                     class="flex items-center justify-center w-full h-full absolute top-0 left-0 bg-gray-100 z-10">
                    <span class="loading loading-dots loading-xl"></span>
                </div>

                <!-- Заголовок чата -->
                <div id="chat-header" class="chat-header flex items-center py-2 relative">
                    <div id="header-photo" class="chat-image avatar w-12 h-12 mr-2">

                    </div>
                </div>

                <div class="divider my-0"></div>

                <!-- Сообщения -->
                <div id="chat-box" class="chat-box flex-1 overflow-y-auto mb-4">
                    <div class="chat chat-start">
                        <div class="chat-image avatar">
                            <div class="skeleton w-10 rounded-full"></div>
                        </div>
                        <div class="chat-bubble skeleton w-36"></div>
                    </div>
                    <div class="chat chat-end">
                        <div class="chat-bubble skeleton w-36"></div>
                    </div>
                </div>


                <p id="typing-indicator"
                   class="text-gray-500 text-sm animate-pulse flex items-center gap-1 mb-2 hidden">
                    Собеседник печатает...
                </p>

                <div id="preview-chat" class="flex overflow-x-auto whitespace-nowrap gap-2 mb-2"></div>

                <div class="flex items-center mt-auto">
                    <input id="chat-input" type="text" class="input input-bordered w-full mr-4 text-sm" autocomplete="off"
                           placeholder="Напишите сообщение..."/>
                    <div class="send-btn flex items-center gap-2">
                        {{--<div class="tooltip tooltip-top" data-tip="Прикрепить файл">
                            <input type="file" id="hiddenFileInput" class="hidden" multiple>
                            <button id="file" class="btn rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/>
                                </svg>
                            </button>
                        </div>--}}
                        <div class="tooltip tooltip-top" data-tip="Отправить">
                            <button id="send-button" class="btn btn-neutral rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('block')
    <dialog id="editPhoto" class="modal">
        <div class="modal-box">
            <img id="modalImage" class="w-full h-full object-contain mb-4" src="" alt="Preview"/>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Отмена</button>
                </form>
            </div>
        </div>
    </dialog>
@endsection

@section('scripts')
    @vite(['resources/js/fetchMessages.js', 'resources/js/fetchListDialogues.js'])
@endsection
