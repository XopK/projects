let currentPage = 1;
let lastPage = false;
let isFetchingMore = false;
let debounceTimeout;
let searchQuery = "";
let categoriesQuery = "";
let levelsQuery = "";
let classQuery = "";
let addressQuery = "";
let sortQuery = "";
let sortFieldQuery = "";

function showScrollLoader() {
    const scrollLoader = document.getElementById('scrollLoader');
    scrollLoader.classList.remove('hidden');
}

function hideScrollLoader() {
    const scrollLoader = document.getElementById('scrollLoader');
    scrollLoader.classList.add('hidden');
}

async function clearTeacherListWithAnimation() {
    const teacherList = document.getElementById('teacher-list');
    const cards = Array.from(teacherList.children);

    cards.forEach(card => {
        card.classList.add('fade-out');
    });

    await new Promise(resolve => setTimeout(resolve, 300));

    teacherList.innerHTML = '';
}

async function fetchTeachers(
    page = currentPage,
    search = searchQuery,
    categories = categoriesQuery,
    levels = levelsQuery,
    classGroup = classQuery,
    address = addressQuery,
    sort = sortQuery,
    field = sortFieldQuery
) {
    if (lastPage || isFetchingMore) return;
    isFetchingMore = true;
    showScrollLoader();

    const teacherList = document.getElementById('teacher-list');

    try {
        const request = await fetch(`/teachers/index?page=${page}
                &search=${encodeURIComponent(search)}
                &categories=${encodeURIComponent(categories)}
                &levels=${encodeURIComponent(levels)}
                &class=${encodeURIComponent(classGroup)}
                &address=${address}
                &sort=${encodeURIComponent(sort)}
                &field=${encodeURIComponent(field)}`);

        const response = await request.json();

        if (response.data.length === 0 && page === 1) {
            teacherList.innerHTML = `
                        <div class="col-span-full text-center text-gray-500 text-lg py-10 flex flex-col items-center justify-center">
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
                            Нет доступных преподавателей
                        </div>
                    `;

            lastPage = true;
            hideScrollLoader();
            return;
        }

        response.data.forEach(teacher => {
            const card = `
                    <a href="/teacher/${teacher.id}" class="mx-auto opacity-0 translate-y-5 transition-all duration-500">
                        <div
                            class="card w-full max-w-[200px] border-none relative group hover:shadow-lg transition-shadow duration-300 flex flex-col items-center p-3">
                            <figure class="w-32 h-32 rounded-full overflow-hidden">
                                <img
                                    src="${teacher.photo_profile}"
                                    alt="${teacher.name}"
                                    class="w-full h-full object-cover"/>
                            </figure>
                            <div class="card-body items-center text-center p-2">
                                <h2 class="card-title text-base font-medium">${teacher.name} ${teacher.nickname ? `${teacher.nickname}` : ''}</h2>
                            </div>
                        </div>
                    </a>`;

            teacherList.insertAdjacentHTML('beforeend', card);

            const newCard = teacherList.lastElementChild;

            void newCard.offsetWidth;

            requestAnimationFrame(() => {
                newCard.classList.remove('opacity-0', 'translate-y-5');
            });
        });

        if (!response.next_page_url) {
            lastPage = true;
            hideScrollLoader();
        } else {
            currentPage++;
        }

    } catch (error) {
        console.error('Ошибка загрузки преподавателей: ', error);
    } finally {
        isFetchingMore = false;
    }

}

document.addEventListener('DOMContentLoaded', () => {
    fetchTeachers(currentPage);

    window.addEventListener('scroll', () => {
        const scrollLoader = document.getElementById('scrollLoader');
        const rect = scrollLoader.getBoundingClientRect();

        if (rect.top < window.innerHeight && !isFetchingMore && !lastPage) {
            fetchTeachers(currentPage);
        }
    });
})

document.getElementById('searchInput').addEventListener('input', async (event) => {
    const search = event.target.value.trim();
    searchQuery = search;

    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(async () => {
        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearTeacherListWithAnimation();
        await fetchTeachers();
    }, 600);
});

document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', async () => {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(cb => cb.value)
            .join(',')

        categoriesQuery = selectedCategories;
        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearTeacherListWithAnimation();
        await fetchTeachers();
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


        await clearTeacherListWithAnimation();
        await fetchTeachers();
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

        await clearTeacherListWithAnimation();
        await fetchTeachers();
    });
});

document.querySelectorAll('input[name="address[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', async () => {
        const selectedAddress = Array.from(document.querySelectorAll('input[name="address[]"]:checked'))
            .map(cb => cb.value)
            .join(',');

        addressQuery = selectedAddress;
        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearTeacherListWithAnimation();
        await fetchTeachers();
    });
});

document.querySelectorAll('#sortDropdown a').forEach(item => {
    item.addEventListener('click', async (e) => {
        e.preventDefault();

        sortQuery = e.target.dataset.sort;
        sortFieldQuery = e.target.dataset.field;

        showScrollLoader();
        currentPage = 1;
        lastPage = false;

        await clearTeacherListWithAnimation();
        await fetchTeachers();
    })
});
