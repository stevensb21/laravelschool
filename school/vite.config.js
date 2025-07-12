import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/nav.css',
                'resources/css/students.css',
                'resources/css/appeals.css',
                'resources/css/homework.css',
                'resources/css/calendar.css',
                'resources/css/statistic.css',
                'resources/css/method.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
