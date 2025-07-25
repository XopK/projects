@extends('layout')
@section('title', 'Восстановление пароля')

@section('style')
@endsection

@section('content')
    <div class="flex items-center justify-center h-[60vh]">
        <div class="card w-lg bg-base-100 card-sm shadow-sm px-5">
            <div class="card-body">
                <h2 class="text-2xl font-bold text-center mb-6">Восстановление пароля</h2>
                <form method="post" action="{{route('resetPasswordUpdate')}}">
                    @csrf
                    @method('PUT')
                    <x-elements.input label="Придумайте новый пароль" name="new_password" id="new-password"
                                      type="password"
                                      placeholder="Введите пароль"/>

                    <x-elements.input label="Подтвердите пароль" name="confirm_password" id="confirm-password"
                                      type="password"
                                      placeholder="Подтвердите пароль"/>

                    <input type="hidden" name="user" value="{{$user}}">
                    <input type="hidden" name="token" value="{{$token}}">

                    <button type="submit" class="btn btn-neutral w-full my-3">
                        Изменить
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('block')
@endsection

@section('scripts')
@endsection

