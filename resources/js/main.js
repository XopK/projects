/*loader*/
import IMask from "imask";
import flatpickr from "flatpickr";
import Plyr from 'plyr';

$(document).ready(function () {
    $('#loader').fadeOut();

    $(window).on('beforeunload', function () {
        $('#loader').fadeIn();
    });
});

document.addEventListener('DOMContentLoaded', () => {
    Plyr.setup('.plyr', {
        controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'], loop: {active: true}
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const banner = document.getElementById('cookie-banner');
    const searchInput = document.getElementById('search-input-header');

    if (!localStorage.getItem('cookieAccepted')) {
        requestAnimationFrame(() => {
            banner.classList.remove('opacity-0', 'translate-y-full');
            banner.classList.add('opacity-100', 'translate-y-0');
        });
    }

    document.getElementById('cookie-accept').addEventListener('click', function () {
        localStorage.setItem('cookieAccepted', 'true');

        banner.classList.remove('opacity-100', 'translate-y-0');
        banner.classList.add('opacity-0', 'translate-y-full');

        setTimeout(() => banner.style.display = 'none', 500);
    });

    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = searchInput.value.trim();
                if (query !== '') {
                    window.location.href = '/groups?search=' + encodeURIComponent(query);
                }
            }
        });
    }
});

if (window.location.pathname.startsWith("/profile")) {
    document.getElementById('avatar-input').addEventListener('change', async function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
        try {
            const formData = new FormData();
            formData.append('avatar', file);

            const response = await fetch('/profile/update/avatar', {
                method: 'POST', body: formData, headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            });

            const result = await response.json();
            console.log('Ответ:', result);

            if (response.ok) {
                showAlert('Аватарка обновлена!', 'success');
            } else if (response.status === 422) {
                const validationErrors = result.errors;
                const avatarError = validationErrors.avatar?.[0];

                if (avatarError) {
                    showAlert(avatarError, 'error');
                } else {
                    showAlert('Ошибка валидации', 'error');
                }
            } else {
                showAlert('Ошибка при загрузки аватарки', 'error');
            }
        } catch (error) {
            console.error('Ошибка запроса:', error);
            showAlert(error.message || 'Неизвестная ошибка', 'error');
        }

    });
}


IMask(document.getElementById('phone'), {
    mask: '+{7}(000)000-00-00'
});

IMask(document.getElementById('email-signIn'), {
    mask: [{
        mask: '+{7}(000)000-00-00'
    }, {
        mask: /^\S*@?\S*$/
    }]
})

/*form signin signup*/
$('#signIn, #signUp').on('click', function () {
    $('#signIn, #signUp').removeClass('btn-outline bg-black text-white');
    $(this).addClass('btn-outline bg-black text-white');

    if (this.id === 'signIn') {
        $('#login').fadeIn(300);
        $('#registration').hide();
    } else {
        $('#registration').fadeIn(300);
        $('#login').hide();
    }
});



