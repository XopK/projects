document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formEditUser');
    const phoneInput = document.getElementById('phoneInput');
    const originalPhoneValue = phoneInput.value;
    const modal = document.getElementById('phoneEdit');
    const cancelBtn = document.getElementById('cancelBtn');
    const submitEdit = document.getElementById('submitEdit');
    const pinInputs = document.querySelectorAll('.pin-input');

    form.addEventListener('submit', async function (e) {
        if (phoneInput.value !== originalPhoneValue) {
            e.preventDefault();

            const spinner = document.getElementById('spinnerEditUser');
            spinner.classList.remove('hidden');

            try {
                const request = await fetch('/profile/updatePhone', {
                    method: 'POST', headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }, body: JSON.stringify({
                        phone: phoneInput.value
                    })
                });

                const result = await request.json();

                if (result.success) {
                    modal.showModal();
                } else {
                    modal.close();
                    showAlert('Ошибка отправки сообщения, попробуйте ещё раз!', 'error');
                }

            } catch (error) {
                modal.close();
                showAlert(error, 'error');
            } finally {
                spinner.classList.add('hidden')
            }
        }
    });

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

    cancelBtn.addEventListener('click', function () {
        phoneInput.value = originalPhoneValue;
        modal.close;
    });

    submitEdit.addEventListener('click', function () {
        const code = Array.from(pinInputs).map(input => input.value).join('');
        document.getElementById('full-code').value = code;

        form.submit();
    });

});

document.querySelectorAll('.notify-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        fetch(this.dataset.url, {
            method: 'POST', headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }, body: JSON.stringify({value: this.checked ? 1 : 0})
        }).then(async response => {
            if (!response.ok) {
                const data = await response.json();
                showAlert(data.message || 'Ошибка', 'error');
            }
        });
    });
});


