@extends('layout')
@section('title', 'Избранное')

@section('style')

@endsection

@section('content')
    <x-profile-menu>
        <div class="card shadow-md p-5 rounded-lg mb-5">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                </svg>

                <div class="text-2xl font-bold select-none">Избранное</div>
            </div>
            <form action="{{route('profileFavorites')}}" method="get">
                <div class="sort-block mb-3 px-2 flex justify-between items-center">
                    <div class="w-full">
                        <label class="floating-label">
                            <input type="text" placeholder="Поиск" class="input input-sm" name="search"
                                   value="{{request('search')}}"/>
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
                            <li>
                                <a href="?sort_field=created_at&sort_direction=desc&search={{ request('search') }}">
                                    Сначала новые
                                </a>
                            </li>
                            <li>
                                <a href="?sort_field=created_at&sort_direction=asc&search={{ request('search') }}">
                                    Сначала старые
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>
            </form>

            <ul class="list">
                @forelse($favorites as $favorite)

                    <li class="list-row flex flex-col sm:flex-row items-center sm:justify-between gap-4 p-2 w-full mb-4 sm:mb-0">
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                            <div class="w-full sm:w-32 sm:h-32 rounded-box overflow-hidden flex-shrink-0">
                                <img class="w-full h-auto sm:w-32 sm:h-32 object-cover"
                                     src="{{$favorite->group->preview ?? $favorite->group->video_preview}}"/>
                            </div>
                            <div class="flex flex-col text-center sm:text-left w-full">
                                <div class="font-medium text-lg">{{$favorite->group->title}}
                                </div>
                                <div
                                    class="description_group text-sm font-semibold opacity-60 mt-2 sm:mt-0 truncate w-64 sm:w-96 mx-auto sm:mx-0">
                                    {{$favorite->group->description}}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">

                            <div class="tooltip tooltip-bottom sm:tooltip-top" data-tip="Страница группы">
                                <a href="{{route('group', $favorite->group->id)}}" class="btn btn-square rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <div class="w-full text-center text-lg text-gray-500 py-5">
                        Нет избранных.
                    </div>
                @endforelse
                    @if($favorites->hasPages())
                        <div class="mt-3">
                            {{ $favorites->links() }}
                        </div>
                    @endif
            </ul>

        </div>
    </x-profile-menu>
@endsection

@section('scripts')

@endsection
