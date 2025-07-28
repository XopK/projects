import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss(), laravel({
        input: [
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/fetchListDialogues.js',
            'resources/js/fetchMessages.js',
            'resources/js/fetchGroups.js',
            'resources/js/profile.js',
            'resources/js/myGroups.js',
            'resources/js/teachersFetch.js',
            'resources/js/teacherProfileFetch.js',
            'resources/js/listUsersFetch.js',
        ],
        refresh: true,
    }),],
});
