<dialog id="authModal" class="modal">
    <div class="modal-box">
        <div class="flex gap-2">
            <button id="signIn" class="btn btn-outline bg-black text-white flex-auto">Авторизация</button>
            <button id="signUp" class="btn flex-auto">Регистрация</button>
        </div>
        <div id="login" class="login py-4">
            <form action="{{route('signIn')}}" method="post">
                @csrf
                <x-elements.input label="Почта или телефон" name="signInData" id="email-signIn" type="text"
                                  isRequired="true"
                                  placeholder="Введите вашу почту или телефон"/>
                <x-elements.input label="Пароль" name="password" id="password-signIn" type="password"
                                  isRequired="true" placeholder="Введите ваш пароль"/>

                <div class="flex items-center justify-between">
                    <div class="rememberMe flex gap-2.5 items-center">
                        <input type="checkbox" name="remember" checked="checked" class="checkbox checkbox-sm my-4"/>
                        <label for="rememberMe">Запомнить меня</label>
                    </div>
                    <a href="{{route('forgotPassword')}}" class="link">Забыли пароль?</a>
                </div>
                <button
                    type="submit"
                    class="btn btn-neutral mt-3 p-7 w-full border-2 border-transparent hover:border-2 hover:border-gray-200 transition-all duration-300 ease-in-out">
                    Войти
                </button>
            </form>
        </div>
        <div id="registration" class="registration py-4">
            <form action="{{ route('signUp') }}" method="post">
                @csrf
                <x-elements.input label="Имя" name="name" id="name" type="text" isRequired="true"
                                  placeholder="Введите имя" value="{{old('name')}}"/>
                <x-elements.input label="Никнейм или фамилия" name="nickname" id="nickname" type="text"
                                  isRequired="true"
                                  placeholder="Введите никнейм или фамилию" value="{{old('nickname')}}"/>
                <x-elements.input label="Почта" name="email" id="email-signUp" type="email" isRequired="true"
                                  placeholder="Введите почту" value="{{old('email')}}"/>
                <x-elements.input label="Дата рождения" name="birthday" id="birthday" type="date" isRequired="true"
                                  value="{{old('birthday')}}" max="{{ date('Y-m-d') }}"/>
                <x-elements.input label="Номер телефона" name="phone" id="phone" type="text" isRequired="true"
                                  placeholder="Введите номер телефона" value="{{old('phone')}}"/>
                <x-elements.input label="Пароль" name="password" id="password" type="password" isRequired="true"
                                  placeholder="Придумайте пароль"
                                  optional="Пароль должен содержать минимум 8 символов."/>
                <x-elements.input label="Подтверждение пароля" name="confirm_password" id="confirm-password"
                                  type="password" isRequired="true"
                                  placeholder="Повторите пароль" optional="Введите пароль снова."/>

                <button
                    type="submit"
                    class="btn btn-neutral mt-3 p-7 w-full border-2 border-transparent hover:border-2 hover:border-gray-200 transition-all duration-300 ease-in-out">
                    Регистрация
                </button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
