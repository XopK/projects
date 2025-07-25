const badgeContainer = document.getElementById('badge-container');
const dropdown = document.getElementById('dropdown');
const placeholder = document.getElementById('placeholder');
const hiddenInput = document.getElementById('selected-directions');
const form = document.getElementById('create-form');
const submitButton = document.getElementById('submit_data');
const loadingSpinner = document.getElementById('loadingSpinner');
const range = document.getElementById('people-range');
const input = document.getElementById('people-input');

let selected = [];  // Массив для хранения объектов с названием и ID
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('category-select');
    const schedule = document.getElementById('schedule');
    const descSpan = document.getElementById('desc-category');
    const status = document.getElementById('is_schedule');

    const selectedOption = select.querySelector(`option[value="${select.value}"]`);

    if (selectedOption) {
        const hasSchedule = selectedOption.dataset.schedule === 'true';
        schedule.style.display = hasSchedule ? 'block' : 'none';
        descSpan.textContent = selectedOption.dataset.desc || '';
        status.value = hasSchedule;
    }

    select.addEventListener('change', function () {
        const checkboxes = schedule.querySelectorAll('input[name="selected_week[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);

        const selectedOption = this.options[this.selectedIndex];

        descSpan.textContent = selectedOption.dataset.desc || '';

        const hasSchedule = selectedOption.dataset.schedule === 'true';
        const hasNotRequired = selectedOption.dataset.notRequired === 'true';

        if (hasSchedule) {
            schedule.style.display = hasSchedule ? 'block' : 'none';
            status.value = hasSchedule;
        }
    });

    badgeContainer.addEventListener('click', toggleDropdown);

    function toggleDropdown() {
        dropdown.classList.toggle('hidden');
    }

    window.selectOption = function (name, id) {
        if (selected.some(item => item.id === id)) return;
        if (selected.length >= 5) return;

        selected.push({name, id});
        updateBadges();
    };

    window.removeOption = function (id) {
        selected = selected.filter(item => item.id !== id);
        updateBadges();
    };

    function updateBadges() {
        badgeContainer.innerHTML = '';
        if (selected.length === 0) {
            placeholder.style.display = 'inline';
            badgeContainer.appendChild(placeholder);
        }

        selected.forEach(item => {
            const badge = document.createElement('div');
            badge.className = 'badge badge-neutral flex items-center gap-1';
            badge.innerHTML = `${item.name} <button onclick="removeOption(${item.id})" class="ml-1 text-sm">✕</button>`;
            badgeContainer.appendChild(badge);
        });

        hiddenInput.value = JSON.stringify(selected);
    }

    document.addEventListener('click', (e) => {
        if (!badgeContainer.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    form.addEventListener('submit', function (e) {
        loadingSpinner.style.display = 'block';
        submitButton.disabled = true;
    });

    range.addEventListener('input', () => {
        input.value = range.value;
    });

    input.addEventListener('input', () => {
        let val = input.value;

        val = val.replace(/\D/g, '');

        if (val === '') {
            input.value = 0;
            return;
        }

        const num = parseInt(val);
        if (num >= 0 && num <= 99) {
            input.value = num;
            range.value = num;
        } else if (num > 99) {
            input.value = 99;
            range.value = 99;
        }
    });

    const oldValue = hiddenInput.value;
    if (oldValue) {
        try {
            const restored = JSON.parse(oldValue);
            if (Array.isArray(restored)) {
                selected = restored;
                updateBadges();
            }
        } catch (e) {
            console.error('Ошибка восстановления направлений:', e);
        }
    }

});

