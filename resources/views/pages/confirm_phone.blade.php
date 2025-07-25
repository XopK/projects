@extends('layout')
@section('title', 'Подтверждение телефона')

@section('content')
    <div class="flex items-center justify-center h-[60vh]">
        <div class="card w-lg bg-base-100 card-sm shadow-sm px-5">
            <div class="card-body">
                <h2 class="text-2xl font-bold text-center mb-6">Подтвердите номер телефона (код приходит в течение 4-5
                    минут)</h2>

                <form id="verify-form" method="POST" action="{{ route('confirmPhoneVerify', ['token' => $token]) }}">
                    @csrf
                    <input type="hidden" name="code" id="full-code"/>

                    <div class="flex justify-center gap-3 mb-4" id="pin-container">
                        @for ($i = 0; $i < 4; $i++)
                            <input type="tel" maxlength="1"
                                   class="pin-input w-12 h-12 text-center border border-gray-300 rounded text-xl focus:outline-none focus:border-neutral focus:ring-2 focus:ring-neutral"/>
                        @endfor
                    </div>

                    <button type="submit" class="btn btn-neutral w-full">Подтвердить</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const pinInputs = document.querySelectorAll('.pin-input');

        pinInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');

                if (e.target.value && index < pinInputs.length - 1) {
                    pinInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                data.split('').forEach((char, i) => {
                    if (i < pinInputs.length) {
                        pinInputs[i].value = char;
                    }
                });
                pinInputs[Math.min(data.length, pinInputs.length - 1)].focus();
            });
        });

        document.getElementById('verify-form').addEventListener('submit', function (e) {
            const code = Array.from(pinInputs).map(input => input.value).join('');
            document.getElementById('full-code').value = code;
        });
    </script>
@endsection
