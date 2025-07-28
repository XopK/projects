document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user') || null;
    const chatBox = document.getElementById('chat-box');
    const chatHeader = document.getElementById('chat-header');
    const headerPhoto = document.getElementById('header-photo');
    const chatContainer = document.getElementById('chat-container');
    const loadingScreen = document.getElementById('loading-screen');
    const chatInput = document.getElementById('chat-input');
    const fileInput = document.getElementById('hiddenFileInput');
    const sendButton = document.getElementById('send-button');
    const fileButton = document.getElementById('file');
    const previewContainer = document.getElementById('preview-chat');
    const editPhoto = document.getElementById('editPhoto');
    const MAX_FILES = 5;

    let chatId = null;
    let userMine = null;
    let userOther = null;
    let currentMessages = {mine: [], other: []};
    let pendingMessageIds = new Set();
    let headerInitialized = false;
    let typingTimer;
    let selectedFiles = [];

    loadingScreen.style.display = 'flex';

    chatInput.addEventListener('input', () => {
        if (chatId && userMine && window.Echo) {
            window.Echo.private(`chat.${chatId}`)
                .whisper('typing', {
                    user_id: userMine.id
                });
        }

        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
        }, 500);
    });

    fileButton.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        const files = Array.from(fileInput.files);
        selectedFiles = [...selectedFiles, ...files];
        renderPreview();
    });

    function initHeader(user) {
        if (!headerInitialized && user) {
            headerPhoto.innerHTML = `<img src="${user.photo_profile}" alt="Avatar" class="rounded-full">`;

            const oldHeaderName = document.getElementById('header-name');
            if (oldHeaderName) {
                oldHeaderName.remove();
            }

            const nameElement = `<h2 id="header-name" class="text-xl font-semibold">${user.name} ${user.nickname ? `${user.nickname}` : ''}</h2>`;
            chatHeader.insertAdjacentHTML('beforeend', nameElement);

            headerInitialized = true;
        }
    }

    function getFileIcon(extension) {
        const icons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            ppt: '📋',
            pptx: '📋',
            txt: '📄',
            zip: '📦',
            rar: '📦',
            '7z': '📦',
            mp3: '🎵',
            wav: '🎵',
            mp4: '🎥',
            avi: '🎥',
            mov: '🎥',
            default: '📎'
        };

        return icons[extension] || icons.default;
    }

    function addMessageToChat(message, isMine) {
        const noMessagesElement = document.getElementById('no-messages');
        if (noMessagesElement) {
            noMessagesElement.remove();
        }

        if (pendingMessageIds.has(message.id)) {
            return;
        }

        const createdAt = new Date(message.created_at);
        const tooltipTime = `${createdAt.getDate()} ${createdAt.toLocaleString('ru-RU', {month: 'long'})} ${createdAt.getFullYear()} ${createdAt.toLocaleTimeString('ru-RU', {
            hour: '2-digit', minute: '2-digit'
        })}`;

        const tooltipSide = isMine ? 'tooltip-left' : 'tooltip-right';

        const rawMessage = message.message || '';
        const escapedMessage = escapeHTML(rawMessage);

        const linkRegex = /(https?:\/\/[^\s]+)/g;
        const messageWithLinks = escapedMessage.replace(linkRegex, (url) => {
            return `<a href="${url}" target="_blank" class="text-blue-500 hover:text-blue-600">${url}</a>`;
        });

        let filesHtml = '';

        if (message.file && message.file !== 'null') {
            try {
                const files = JSON.parse(message.file);
                if (Array.isArray(files) && files.length > 0) {
                    filesHtml = files.map(url => {
                        // Получаем имя файла из URL
                        const fileName = url.split('/').pop() || 'файл';
                        const fileExtension = fileName.split('.').pop()?.toLowerCase() || '';

                        // Проверяем, является ли файл изображением
                        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                        const isImage = imageExtensions.includes(fileExtension);

                        if (isImage) {
                            // Отображаем изображение как раньше
                            return `
                            <div class="p-1">
                                <a href="${url}" data-fancybox="gallery-${message.id}">
                                    <img src="${url}" alt="image"
                                         class="w-full max-w-[500px] sm:max-w-[400px] md:max-w-[300px] rounded-lg shadow-md object-cover"/>
                                </a>
                            </div>
                        `;
                        } else {
                            // Отображаем файл для скачивания
                            const fileIcon = getFileIcon(fileExtension);
                            return `
                            <div class="p-2 border border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                <a href="${url}" download="${fileName}" target="_blank" class="flex items-center gap-3 text-gray-700 hover:text-blue-600 no-underline">
                                    <span class="text-2xl">${fileIcon}</span>
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">${fileName}</div>
                                        <div class="text-xs text-gray-500">Нажмите для скачивания</div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>
                        `;
                        }
                    }).join('');
                }
            } catch (e) {
                console.error('Ошибка при парсинге message.file:', e, message.file);
            }
        }

        const messageElement = `
            <div class="chat ${isMine ? 'chat-end' : 'chat-start'} message ${message.is_read ? 'read' : ''}" data-message-id="${message.id}" id="message-${message.id}">
                ${!isMine ? `
                    <div class="chat-image avatar">
                        <div class="w-10 rounded-full">
                            <img alt="${userOther.name}" src="${userOther.photo_profile}"/>
                        </div>
                    </div>
                ` : ''}
                <div class="chat-bubble">
                    ${filesHtml}
                    ${messageWithLinks}
                </div>
                <div class="chat-footer mt-2 flex items-center justify-content-between gap-1">
                ${isMine ? `
                  <span class="text-xs ${message.is_read ? 'text-blue-500' : 'text-gray-400'}">
                    ${message.is_read ? '✔' : '✔'}
                  </span>
                ` : ''}
                    <div class="tooltip ${tooltipSide}" data-tip="${tooltipTime}">
                      <time class="text-xs opacity-60">
                        ${createdAt.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'})}
                      </time>
                    </div>
                </div>
            </div>
        `;

        chatBox.insertAdjacentHTML('beforeend', messageElement);

        chatBox.scrollTop = chatBox.scrollHeight;
        pendingMessageIds.add(message.id);
    }

    function renderMessages() {
        chatBox.innerHTML = '';
        pendingMessageIds.clear();

        if (currentMessages.mine.length === 0 && currentMessages.other.length === 0) {
            const noMessages = `
                <div id="no-messages" class="flex items-center justify-center h-full">
                    <span class="text-xl text-gray-500 opacity-60">Где все? Тут пусто!</span>
                </div>
            `;
            chatBox.insertAdjacentHTML('afterbegin', noMessages);
            return;
        }

        const allMessages = [...currentMessages.other, ...currentMessages.mine];
        allMessages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

        let lastDate = null;

        allMessages.forEach(message => {
            const messageDate = new Date(message.created_at);
            const messageDateString = messageDate.toLocaleDateString('ru-RU', {
                year: 'numeric', month: 'long', day: 'numeric'
            });

            if (lastDate !== messageDateString) {
                const divider = `
                <div class="divider text-center text-sm text-gray-500 my-4">${messageDateString}</div>
            `;
                chatBox.insertAdjacentHTML('beforeend', divider);
                lastDate = messageDateString;
            }

            const isMine = message.user_id === userMine.id;
            addMessageToChat(message, isMine);
        });
    }

    function getMessages() {
        fetch(`/messages?user=${userId}`)
            .then(response => {
                if (response.status === 204) {
                    return;
                }

                if (response.status === 206) {
                    chatContainer.innerHTML = '';
                    const noDialoge = `
                        <div class="flex flex-col justify-center items-center w-full h-full text-xl text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5"
                                 stroke="currentColor"
                                 class="w-20 h-20 mb-4">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <div>Выберите диалог</div>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('afterbegin', noDialoge);
                    return;
                }

                return response.json();
            })
            .then(messages => {
                if (!messages) {
                    return;
                }

                const {mine, other, user_mine, user_other, chat_id} = messages;
                chatId = chat_id;
                userMine = user_mine;
                userOther = user_other;
                currentMessages = {mine, other};

                // Инициализируем шапку только один раз при получении данных
                initHeader(userOther);

                if (!window.chatInitialized) {
                    window.Echo.private(`chat.${chatId}`)
                        .listen('Chat\\SendMessage', (e) => {
                            const isMine = e.message.user_id === userMine.id;
                            if (!isMine) {
                                currentMessages.other.push(e.message);
                                addMessageToChat(e.message, false);
                                observeUnreadMessages();
                            }
                        }).listenForWhisper('typing', (e) => {
                        if (e.user_id !== userMine.id) {
                            const indicator = document.getElementById('typing-indicator');
                            indicator.classList.remove('hidden');

                            clearTimeout(window.typingIndicatorTimeout);
                            window.typingIndicatorTimeout = setTimeout(() => {
                                indicator.classList.add('hidden');
                            }, 1000);
                        }
                    }).listen('Chat\\ReadMessages', (e) => {
                        const messageId = e.message.id;
                        const el = document.querySelector(`[data-message-id="${messageId}"]`);

                        if (el && el.classList.contains('chat-end')) {
                            el.classList.add('read');

                            const footer = el.querySelector('.chat-footer span');
                            if (footer) {
                                footer.classList.remove('text-gray-400');
                                footer.classList.add('text-blue-500');
                                footer.textContent = '✔';
                            }
                        }
                    });
                    window.chatInitialized = true;
                }

                renderMessages();
                observeUnreadMessages();

            })
            .catch((error) => {
                console.error('Ошибка получения сообщений:', error);
            })
            .finally(() => {
                loadingScreen.classList.add('fade-out');
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            });
    }

    getMessages();

    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });

    function observeUnreadMessages() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const messageId = entry.target.dataset.messageId;

                    markMessageAsRead(messageId);

                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.6
        });

        document.querySelectorAll('.message:not(.read)').forEach(el => {
            const isMine = el.classList.contains('chat-end');
            if (!isMine) observer.observe(el);
        });
    }

    function markMessageAsRead(messageId) {
        fetch('/markReadMessage', {
            method: 'POST', headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }, body: JSON.stringify({message_id: messageId})
        }).then(() => {
            const el = document.querySelector(`[data-message-id="${messageId}"]`);
            if (el) el.classList.add('read');
        });
    }

    function sendMessage() {
        const messageText = chatInput.value.trim();
        const filesPost = fileInput.files;

        if (messageText !== '' || filesPost.length > 0) {
            const formData = new FormData();

            formData.append('message', messageText);
            formData.append('chatId', chatId);

            for (let i = 0; i < filesPost.length; i++) {
                formData.append('files[]', filesPost[i]);
            }

            fetch('/sendMessage', {
                method: 'POST', headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }, body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    const newMessage = {
                        ...data, user_id: userMine.id, is_read: 0, chat_id: chatId
                    };
                    currentMessages.mine.push(newMessage);
                    addMessageToChat(newMessage, true);
                });

            chatInput.value = '';
            fileInput.value = '';
            selectedFiles = [];
            renderPreview();
        }
    }

    function escapeHTML(str) {
        return str.replace(/[&<>"']/g, (match) => {
            const escapeMap = {
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
            };
            return escapeMap[match];
        });
    }

    function renderPreview() {
        previewContainer.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                if (file.type.startsWith('image/')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-40 aspect-[4/3] overflow-hidden shrink-0';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full object-cover';

                    img.addEventListener('click', function () {
                        const modalImage = document.getElementById('modalImage');
                        modalImage.src = e.target.result;
                        editPhoto.showModal();
                    });

                    const deleteBtn = document.createElement('button');

                    deleteBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>`;

                    deleteBtn.className = 'absolute top-1 right-1 bg-black/60 text-white text-sm rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600';
                    deleteBtn.addEventListener('click', function () {
                        selectedFiles.splice(index, 1);
                        renderPreview();
                    });

                    wrapper.appendChild(img);
                    wrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(wrapper);
                } else {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative w-40 aspect-[4/3] bg-gray-100 border border-gray-300 rounded flex flex-col items-center justify-center text-center text-sm p-2 shrink-0';

                    const icon = document.createElement('div');
                    icon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>`;

                    const fileName = document.createElement('p');
                    fileName.textContent = file.name;
                    fileName.className = 'truncate max-w-full';


                    const deleteBtn = document.createElement('button');
                    deleteBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>`;

                    deleteBtn.className = 'absolute top-1 right-1 bg-black/60 text-white text-sm rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600';
                    deleteBtn.addEventListener('click', function () {
                        selectedFiles.splice(index, 1);
                        renderPreview();
                    });

                    wrapper.appendChild(icon);
                    wrapper.appendChild(fileName);
                    wrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(wrapper);
                }
            };

            reader.readAsDataURL(file);
        });

        updateInputFiles();
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }
});
