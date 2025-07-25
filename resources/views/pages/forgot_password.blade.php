@extends('layout')
@section('title', 'Восстановление пароля')

@section('style')
@endsection

@section('content')
    <div class="flex items-center justify-center h-[60vh]">
        <div class="card w-lg bg-base-100 card-sm shadow-sm px-5">
            <div class="card-body">
                <h2 class="text-2xl font-bold text-center mb-6">Восстановление пароля</h2>
                <form id="EmailForm">
                    <x-elements.input label="Почта" name="email" id="email-reset" type="email"
                                      placeholder="Введите почту которую указывали при регистрации"/>

                    <button type="submit" class="btn btn-neutral w-full my-3" id="resetButton">
                        <span class="loading loading-spinner hidden"></span>
                        Отправить ссылку для сброса
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('block')
    <dialog id="notifyModal" class="modal">
        <div class="modal-box">
            <div class="flex items-center">
                <h3 class="text-lg font-bold">Письмо отправлено</h3>
            </div>
            <p class="py-4">
                Ссылка для сброса пароля была отправлена на указанную почту.
                Пожалуйста, проверьте ваш почтовый ящик, включая папку «Спам». <br>
                Если письмо не пришло в течение нескольких минут, убедитесь, что вы указали правильный адрес,
                или повторите попытку позже.
            </p>
            <div class="modal-action">
                <form class="w-full" method="dialog">
                    <button class="btn btn-success w-full">Ок</button>
                </form>
            </div>
        </div>
    </dialog>
@endsection

@section('scripts')
    <script>
        const form = document.getElementById('EmailForm');
        const submitBtn = document.getElementById('resetButton');
        const notifyModal = document.getElementById('notifyModal');
        const originalHTML = submitBtn.innerHTML;
        const COOLDOWN_SECONDS = 60;
        const STORAGE_KEY = 'password_reset_time';

        function startCooldown(remaining) {
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-disabled');

            const interval = setInterval(() => {
                if (remaining <= 0) {
                    clearInterval(interval);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-disabled');
                    submitBtn.innerHTML = originalHTML;
                    localStorage.removeItem(STORAGE_KEY);
                } else {
                    submitBtn.textContent = `Повторно через ${remaining--} сек.`;
                }
            }, 1000);
        }

        const lastSentTime = localStorage.getItem(STORAGE_KEY);
        if (lastSentTime) {
            const secondsPassed = Math.floor((Date.now() - parseInt(lastSentTime)) / 1000);
            const remaining = COOLDOWN_SECONDS - secondsPassed;
            if (remaining > 0) {
                startCooldown(remaining);
            } else {
                localStorage.removeItem(STORAGE_KEY);
            }
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const spinner = submitBtn.querySelector('.loading-spinner');
            spinner.classList.remove('hidden');
            submitBtn.disabled = true;

            try {
                const response = await fetch('/forgotPassword', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email-reset').value
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors && result.errors.email) {
                        showAlert(result.errors.email[0], 'error');
                    } else {
                        showAlert(result.message || 'Произошла ошибка', 'error');
                    }
                    return;
                }

                localStorage.setItem(STORAGE_KEY, Date.now().toString());
                startCooldown(COOLDOWN_SECONDS);

                notifyModal.showModal();

            } catch (error) {
                showAlert(error || 'Произошла ошибка', 'error');
            } finally {
                spinner.classList.add('hidden');
                submitBtn.disabled = false;
            }

        });
    </script>
@endsection

