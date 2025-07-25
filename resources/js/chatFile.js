document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('hiddenFileInput');
    const fileButton = document.getElementById('file');
    const previewContainer = document.getElementById('preview-chat');
    const editPhoto = document.getElementById('editPhoto');

    let selectedFiles = [];

    fileButton.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        const files = Array.from(fileInput.files);
        selectedFiles = [...selectedFiles, ...files];
        renderPreview();
    });

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
