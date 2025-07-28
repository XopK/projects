document.addEventListener('DOMContentLoaded', function () {
    fetch('/getChat')
        .then(response => response.json())
        .then(chats => {
            const chatList = document.getElementById('chat-list');
            const chatListMobile = document.getElementById('chat-list-mobile');

            if (chats.length === 0) {
                const noChat = `
                            <li class="p-3 rounded-lg bg-white shadow mb-2 flex flex-col">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-sm font-medium text-center w-full">Чаты отсутствуют</span>
                                </div>
                            </li>
                        `;

                chatList.insertAdjacentHTML('beforeend', noChat);
                chatListMobile.insertAdjacentHTML('beforeend', noChat);

            } else {
                chats.forEach(chat => {
                    const chatTime = new Date(chat.last_message.created_at).toLocaleTimeString('ru-RU', {
                        hour: '2-digit', minute: '2-digit'
                    });
                    let lastMessageText = 'Нет сообщений';

                    if (chat.last_message) {
                        const hasText = !!chat.last_message.message;
                        const hasFiles = chat.last_message.file && chat.last_message.file !== 'null';

                        if (hasText) {
                            lastMessageText = chat.last_message.message;
                        } else if (hasFiles) {
                            try {
                                const files = JSON.parse(chat.last_message.file);
                                if (Array.isArray(files) && files.length > 0) {
                                    lastMessageText = `${files.length} вложени${files.length === 1 ? 'е' : (files.length < 5 ? 'я' : 'й')}`;
                                }
                            } catch (e) {
                                console.error('Ошибка при разборе вложений:', e);
                            }
                        }
                    }

                    const chatItem = `
                                <a href="/chat?user=${chat.receiver.id}">
                                    <li class="p-3 rounded-lg bg-white shadow mb-2 cursor-pointer flex items-center justify-between hover:shadow-lg transform hover:scale-102 transition-all duration-200 ease-in-out">
                                        <div class="flex items-center flex-wrap gap-3">
                                            <img src="${chat.receiver.photo_profile}"
                                                class="w-10 h-10 rounded-full" alt="${chat.receiver.name} ${chat.receiver.nickname ? `${chat.receiver.nickname}` : ''}">
                                            <div class="text-block-message">
                                                <span class="text-sm font-medium">${chat.receiver.name} ${chat.receiver.nickname ? `${chat.receiver.nickname}` : ''}</span>
                                                <p class="text-xs text-gray-500 truncate w-[100px]">${lastMessageText}</p>
                                            </div>
                                        </div>
                                    <time class="text-xs opacity-60 ml-auto">${chatTime}</time>
                                    </li>
                                </a>
                            `;

                    const chatItemMobile = `
                                <a href="/chat?user=${chat.receiver.id}">
                                <li class="p-3 rounded-lg bg-white shadow mb-2 cursor-pointer flex flex-col">
                                <div class="flex flex-wrap items-center gap-2">
                                    <img
                                        src="${chat.receiver.photo_profile}"
                                        class="w-10 h-10 rounded-full mr-3" alt="${chat.receiver.name} ${chat.receiver.nickname ? `${chat.receiver.nickname}` : ''}">
                                    <div class="text-block-message">
                                        <span class="text-sm font-medium w-1/2">${chat.receiver.name} ${chat.receiver.nickname ? `${chat.receiver.nickname}` : ''}</span>
                                        <p class="text-xs text-gray-500 truncate w-[100px]">${lastMessageText}</p>
                                    </div>
                                    <time class="text-xs opacity-60 self-end">${chatTime}</time>
                                </div>
                                </li>
                                </a>
                            `;

                    chatList.insertAdjacentHTML('beforeend', chatItem);
                    chatListMobile.insertAdjacentHTML('beforeend', chatItemMobile);
                });
            }
        })
});
