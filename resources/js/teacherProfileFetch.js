moment.locale('ru');
const teacher = window.teacherId
let currentPage = 1;
let lastPage = false;
let isFetchingMore = false;
let debounceTimeout;
let searchQuery = "";
let categoriesQuery = "";
let levelsQuery = "";
let classQuery = "";
let currentSort = '';
let sortField = '';
const classTransl = {
    regular_group: 'Регулярная группа',
    course: 'Курс',
    intensive: 'Интенсив',
    class: 'Класс',
    private_lesson: 'Индивидуальное занятие',
    guest_masterclass: 'Привозной мастер-класс',
};

document.addEventListener('click', async (e) => {
    if (e.target.closest('.share-btn')) {
        const btn = e.target.closest('.share-btn');
        const groupId = btn.dataset.id;
        const shareUrl = `${location.origin}/group/${groupId}`;

        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Курс по танцам', text: 'Присоединяйся группе!', url: shareUrl,
                });
            } catch (err) {
                console.warn('Share отменён или ошибка:', err);
            }
        } else {
            try {
                await navigator.clipboard.writeText(shareUrl);
                alert('Ссылка скопирована!');
            } catch {
                prompt('Скопируйте ссылку вручную:', shareUrl);
            }
        }
    }
});

function showScrollLoader() {
    const scrollLoader = document.getElementById('scrollLoader');
    scrollLoader.classList.remove('hidden');
}

function hideScrollLoader() {
    const scrollLoader = document.getElementById('scrollLoader');
    scrollLoader.classList.add('hidden');
}

async function clearGroupCardsWithAnimation() {
    const cards = document.querySelectorAll('#group-list > div');
    const promises = [];

    cards.forEach(card => {
        card.classList.add('fade-out');

        const promise = new Promise(resolve => {
            card.addEventListener('transitionend', () => {
                card.remove();
                resolve();
            }, {once: true});
        });

        promises.push(promise);
    });

    await Promise.all(promises);
}


async function fetchGroups(page = 1, search = searchQuery, categories = categoriesQuery, levels = levelsQuery, classes = classQuery, sort = currentSort, field = sortField) {
    if (lastPage || isFetchingMore) return;

    isFetchingMore = true;

    const groupList = document.getElementById('group-list');

    try {
        const request = await fetch(`/teacher/${teacher}/videos?page=${page}
                &search=${encodeURIComponent(search)}
                &categories=${encodeURIComponent(categories)}
                &levels=${encodeURIComponent(levels)}
                &class=${encodeURIComponent(classes)}
                &sort=${encodeURIComponent(sort)}
                &field=${encodeURIComponent(field)}`);

        const response = await request.json();

        if (response.data.length === 0 && page === 1) {
            groupList.innerHTML = `
                        <div class="col-span-full text-center text-gray-500 text-lg py-50 flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-16 h-16 mb-3 text-gray-400">
                                <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                            Нет доступных постов
                        </div>
                    `;
            hideScrollLoader();
            lastPage = true;
            return;
        }

        response.data.forEach(group => {
            const card = `
                    <div class="bg-white rounded-lg shadow-lg opacity-0 translate-y-5 transition-all duration-500">
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center space-x-3">
                                <img
                                    src="${group.user.photo_profile}"
                                    alt="${group.user.name}" class="w-10 h-10 rounded-full">
                                <span class="font-semibold">${group.user.name} ${group.user.nickname ? `${group.user.nickname}` : ''}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-400 space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                                </svg>
                                <span>${group.created_diff}</span>
                            </div>
                        </div>

                        <div class="video-wrapper relative aspect-9/16">

                            <video playsinline loop
                                   class="custom-video w-full h-full object-cover"
                                   poster="${group.video_preview}">
                                   <source src="${group.video_group}" type="video/mp4">
                            </video>

                            <!-- Кнопка воспроизведения -->
                            <button
                                class="play-overlay absolute inset-0 flex items-center justify-center text-white text-5xl bg-black/30">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="size-20">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                                </svg>
                            </button>

                            <!-- Контейнер громкости -->
                            <div class="volume-control absolute bottom-4 right-4">
                            <!-- Контейнер иконки с ползунком -->
                            <div class="relative flex flex-col items-center">
                                <!-- Иконка -->
                                <button
                                    class="volume-icon bg-black/50 p-2 rounded-full text-white hover:bg-black/70 transition z-20 relative">
                                    <!-- unmute -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6 unmute-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z"/>
                                    </svg>
                                    <!-- mute -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mute-icon hidden">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75 19.5 12m0 0 2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6 4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                    </svg>
                                </button>
                            </div>
                            </div>

                        </div>

                        <div class="flex justify-between items-center p-4">
                            <div class="flex space-x-4">

                                <div class="tooltip size-8 sm:size-7"
                                     data-tip="${group.isFavorite ? 'Убрать из избранного' : 'Избранное'}">
                                    <button
                                        class="favorite-btn hover:text-red-500 ${group.isFavorite ? 'text-red-500' : ''}"
                                        data-id="${group.id}" data-favorite="${group.isFavorite}">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             fill="${group.isFavorite ? 'currentColor' : 'none'}" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-8 sm:size-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="tooltip size-8 sm:size-7" data-tip="Чат">
                                    <a href="/chat?user=${group.user.id}" class="hover:text-gray-500">

                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 sm:size-7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                       </svg>

                                    </a>
                                </div>

                                <div class="tooltip size-8 sm:size-7" data-tip="Поделиться">
                                    <button class="share-btn hover:text-gray-500" data-id="${group.id}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5"
                                             stroke="currentColor" class="size-8 sm:size-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/>
                                        </svg>

                                    </button>
                                </div>

                            </div>
                            <div class="flex space-x-4">
                                <a href="/group/${group.id}" class="btn btn-neutral btn-sm">Подробнее</a>
                            </div>
                        </div>

                        <div class="desc-post pb-4 px-4">
                            <div
                                class="collapsible-content text-sm text-gray-700 space-y-2 transition-all duration-300 ease-in-out line-clamp-2">
                                <div class="text-lg font-bold text-gray-800">${group.title}</div>

                                <p class="text-gray-600 whitespace-pre-line">${group.description.replace(/^[\r\n]+/, '')}</p>

                                <div class="divider m-0"></div>

                                <div class="space-y-1 pt-1">
                                    <div><span class="font-semibold">Направления: </span>${group.categories.map(category => category.name).join(', ')}</div>
                                    <div><span class="font-semibold">Категория: </span>${classTransl[group.class] || group.class}</div>
                                    ${group.readable_schedule ? `<div><span class="font-semibold">Расписание: </span>${group.readable_schedule}</div>` : ''}
                                    ${group.date ? `<div><span class="font-semibold">Дата старта:</span> ${moment(`${group.date}`).format('D MMMM')}</div>` : ''}
                                    ${group.date_end ? `<div><span class="font-semibold">Дата окончания:</span> ${moment(`${group.date_end}`).format('D MMMM')}</div>` : ''}
                                    ${group.duration ? `<div><span class="font-semibold">Продолжительность: </span>${group.duration} мин</div>` : ''}
                                    ${group.price ? `<div><span class="font-semibold">Цена:</span> ${group.price} ₽</div>` : ''}
                                    ${group.address ? `<div class="font-semibold text-gray-800">${group.address.studio_name} — ${group.address.studio_address}</div>` : ''}
                                </div>
                            </div>

                            <button class="toggle-btn text-blue-500 text-sm mt-1 hover:underline">Показать полностью
                            </button>
                        </div>

                    </div>
                    `;

            groupList.insertAdjacentHTML('beforeend', card);

            const newCard = groupList.lastElementChild;

            void newCard.offsetWidth;

            requestAnimationFrame(() => {
                newCard.classList.remove('opacity-0', 'translate-y-5');
            });
        });

        initVideoControls();

        if (!response.next_page_url) {
            lastPage = true;
            hideScrollLoader();
        } else {
            currentPage++;
        }

    } catch (error) {
        console.error('Ошибка загрузки групп:', error);
    } finally {
        isFetchingMore = false;
    }
}

document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.favorite-btn');
    if (!btn) return;

    const groupId = btn.dataset.id;
    const isFavor = btn.dataset.favorite === 'true';

    try {
        const response = await fetch(`/group/${groupId}/favorite`, {
            method: 'POST', headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.status === 403) {
            showAlert('Пожалуйста, авторизуйтесь для добавления в избранное.', 'error');
            return;
        }

        const data = await response.json();

        if (!response.ok) {
            return showAlert(data.message || 'Произошла ошибка', 'error');
        }

        btn.dataset.favorite = data.action === 'add' ? 'true' : 'false';
        btn.classList.toggle('text-red-500', data.action === 'add');

        // Обновление иконки <svg>
        const svg = btn.querySelector('svg');
        svg.setAttribute('fill', data.action === 'add' ? 'currentColor' : 'none');

        // Обновление data-tip на родительском tooltip (если нужно)
        const tooltip = btn.closest('.tooltip');
        if (tooltip) {
            tooltip.dataset.tip = data.action === 'add' ? 'Убрать из избранного' : 'Избранное';
        }

    } catch (error) {
        showAlert(error.message || 'Ошибка сети', 'error');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    fetchGroups(currentPage);

    window.addEventListener('scroll', () => {
        const scrollLoader = document.getElementById('scrollLoader');
        const rect = scrollLoader.getBoundingClientRect();

        if (rect.top < window.innerHeight && !isFetchingMore && !lastPage) {
            fetchGroups(currentPage);
        }
    });
})

document.addEventListener('click', (event) => {
    if (event.target.classList.contains('toggle-btn')) {
        const btn = event.target;
        const container = btn.closest('.desc-post');
        const collapsible = container.querySelector('.collapsible-content');

        collapsible.classList.toggle('line-clamp-2');

        btn.textContent = collapsible.classList.contains('line-clamp-2') ? 'Показать полностью' : 'Свернуть';
    }
});

function initVideoControls() {
    const allVideos = Array.from(document.querySelectorAll('.custom-video'));

    document.querySelectorAll('.video-wrapper:not([data-initialized])').forEach(wrapper => {
        const video = wrapper.querySelector('.custom-video');
        const overlayBtn = wrapper.querySelector('.play-overlay');
        const volumeIcon = wrapper.querySelector('.volume-icon');

        if (!video || !overlayBtn || !volumeIcon) return;

        // Отметим, что элемент уже инициализирован
        wrapper.setAttribute('data-initialized', 'true');

        video.muted = false;

        if (video.paused) {
            overlayBtn.classList.remove('hidden');
        }

        video.addEventListener('play', () => {
            overlayBtn.classList.add('hidden');

            allVideos.forEach(v => {
                if (v !== video && !v.paused) {
                    v.pause();
                }
            });
        });

        video.addEventListener('pause', () => {
            overlayBtn.classList.remove('hidden');
            video.pause();
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting && !entry.target.paused) {
                    entry.target.pause();
                }
            });
        }, {threshold: 0.5});

        observer.observe(video);

        overlayBtn.addEventListener('click', () => {
            video.play();
        });

        video.addEventListener('click', () => {
            video.paused ? video.play() : video.pause();
        });

        volumeIcon.addEventListener('click', () => {
            video.muted = !video.muted;

            const muteIcon = volumeIcon.querySelector('.mute-icon');
            const unmuteIcon = volumeIcon.querySelector('.unmute-icon');

            if (video.muted) {
                muteIcon.classList.remove('hidden');
                unmuteIcon.classList.add('hidden');
            } else {
                muteIcon.classList.add('hidden');
                unmuteIcon.classList.remove('hidden');
            }
        });
    });
}

document.getElementById('search-input').addEventListener('input', async (event) => {
    const query = event.target.value.trim();
    searchQuery = query;

    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(async () => {
        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearGroupCardsWithAnimation();
        await fetchGroups();

    }, 600);
});

document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', async () => {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(cb => cb.value)
            .join(',');

        categoriesQuery = selectedCategories;

        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearGroupCardsWithAnimation();
        await fetchGroups();

    });
});

document.querySelectorAll('input[name="levels[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', async () => {
        const selectedLevels = Array.from(document.querySelectorAll('input[name="levels[]"]:checked'))
            .map(cb => cb.value)
            .join(',');

        levelsQuery = selectedLevels;
        showScrollLoader();
        currentPage = 1;
        lastPage = false;


        await clearGroupCardsWithAnimation();
        await fetchGroups();

    });
});

document.querySelectorAll('input[name="class[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', async () => {
        const selectedClass = Array.from(document.querySelectorAll('input[name="class[]"]:checked'))
            .map(cb => cb.value)
            .join(',');

        classQuery = selectedClass;
        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearGroupCardsWithAnimation();
        await fetchGroups();

    });
});

document.querySelectorAll('#sortDropdown a').forEach(item => {
    item.addEventListener('click', async (e) => {
        e.preventDefault();

        currentSort = e.target.dataset.sort;
        sortField = e.target.dataset.field;

        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearGroupCardsWithAnimation();
        await fetchGroups();
    })
});
