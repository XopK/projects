document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.listUsersBtn');
    const modal = document.getElementById('listUsers');
    const listUserContainer = document.getElementById('list-user-container');
    const titleGroupList = document.getElementById('titleGroupList');
    const searchInputList = document.getElementById('searchList');
    const confirmDeleteList = document.getElementById('confirmDeleteList');

    let userName = '';
    let denyUserId = null;
    let allUsers = [];
    let currentGroupId = null;

    buttons.forEach((btn) => {
        btn.addEventListener('click', async () => {

            currentGroupId = btn.dataset.groupId;
            const groupName = btn.dataset.groupName;

            titleGroupList.innerText = `Список пользователей группы "${groupName}"`;

            const originalContent = btn.innerHTML;

            btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span>';
            btn.disabled = true;

            try {
                const response = await fetch(`/profile/users-list/${currentGroupId}`);
                const data = await response.json();

                allUsers = data.users;

                renderList(allUsers);

                modal.showModal();
            } catch (error) {
                modal.showModal();
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        });
    });

    searchInputList.addEventListener('input', () => {
        const query = searchInputList.value.toLowerCase().trim();

        const filteredUsers = allUsers.filter(user => user.name.toLowerCase().includes(query) || user.nickname.toLowerCase().includes(query));

        renderList(filteredUsers);
    });

    document.querySelectorAll('input[name="filter_app"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const value = radio.value;

            let filteredUsers = allUsers;

            if (value == "pending") {
                filteredUsers = allUsers.filter(user => user.status_confirm != 1);
            } else if (value == "confirmed") {
                filteredUsers = allUsers.filter(user => user.status_confirm == 1);
            }

            renderList(filteredUsers);
            document.getElementById('dropdownToggle').innerText = radio.nextElementSibling.innerText;
        });
    });

    document.addEventListener('click', async (e) => {
        const btnAction = e.target.closest('.btn-apply, .btn-deny, .btn-return');
        if (btnAction) {
            const action = btnAction.dataset.action;
            const userId = btnAction.dataset.userId;

            if (action === 'deny') {
                const userDeleteList = document.getElementById('userDeleteList');

                userName = btnAction.dataset.userName;
                denyUserId = userId;
                userDeleteList.innerHTML = userName;

                confirmDeleteList.showModal();
                return;
            }

            if (action === 'apply') {
                await updateList(action, userId);
            }

            if (action === 'return') {
                await updateList(action, userId);
            }

        }
    });

    document.getElementById('acceptDeleteList').addEventListener('click', async function () {
        await updateList('deny', denyUserId);

        confirmDeleteList.close();
    });

    async function updateList(action, userId) {
        try {
            const response = await fetch(`/profile/users-list/${currentGroupId}/update`, {
                method: 'POST', headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }, body: JSON.stringify({
                    action: action, userId: userId
                })
            });
            const data = await response.json();

            if (data.action == 'denied') {
                allUsers = allUsers.filter(user => user.id != userId);
            }

            if (data.action == 'applied') {
                allUsers = allUsers.map(user => {
                    if (user.id == userId) {
                        return {
                            ...user, status_confirm: 1
                        };
                    }
                    return user;
                });
            }

            if (data.action == 'returned') {
                allUsers = allUsers.map(user => {
                    if (user.id == userId) {
                        return {
                            ...user, status_confirm: 0
                        };
                    }
                    return user;
                });
            }

            renderList(allUsers);

        } catch (error) {
            console.error(error);
        }
    }

    function renderList(users) {
        const list = listUserContainer.querySelector('.list');
        list.innerHTML = '';

        if (users.length === 0) {
            list.innerHTML = `
            <li class="text-center text-gray-400 py-4 mt-3 text-lg">
                Нет результатов
            </li>
            `;
        }

        users.forEach(user => {
            const statusText = user.status_confirm == 1 ? "Подтверждена" : "Не подтверждена";
            const actionBtn = user.status_confirm == 1 ? "" : `
            <div class="tooltip" data-tip="Принять">
                <button class="btn btn-sm btn-success btn-circle btn-apply" data-user-id="${user.id}" data-action="apply">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </button>
            </div>

            <div class="tooltip" data-tip="Отклонить">
                <button class="btn btn-sm btn-error btn-circle btn-deny" data-user-id="${user.id}" data-action="deny" data-user-name="${user.name} ${user.nickname}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            `;

            const optionBackLink = user.status_confirm != 1 ? "" : `
            <li>
                <button class="btn-return" data-user-id="${user.id}" data-action="return">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                    </svg>
                    Вернуть в заявки
                </button>
            </li>
            `;

            const userItem = `
                <li class="list-row flex items-center justify-between px-0">
                        <a class="flex items-center gap-2" href="/user/${user.id}">
                            <div class="avatar">
                                <div class="size-15 rounded-full overflow-hidden">
                                    <img src="${user.photo_profile}" alt="${user.name} ${user.nickname}"/>
                                </div>
                            </div>
                            <div>
                                <div class="font-semibold">${user.name} ${user.nickname}</div>
                                <div class="text-xs font-semibold opacity-60">${statusText}</div>
                            </div>
                        </a>

                        <div class="flex items-center gap-2">
                            ${actionBtn}

                            <div class="dropdown dropdown-left dropdown-center">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>
                                    </svg>

                                </div>
                                <ul tabindex="0"
                                    class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">

                                    ${optionBackLink}

                                    <li>
                                        <a href="/chat?user=${user.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                                            </svg>
                                            Чат
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/user/${user.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                            </svg>

                                            Пользователь
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                `;
            list.insertAdjacentHTML('beforeend', userItem);
        });
    }
});
