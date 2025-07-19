import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
		input: [
			'resources/css/student/dashboard.css',
			'resources/css/teacher/dashboard.css',
			'resources/css/appeal.css',
			'resources/css/colors.css',
			'resources/css/management.css',
			'resources/css/method-modal.css',
			'resources/css/reviews.css',
			'resources/css/teachers.css',
                	'resources/css/app.css',
                	'resources/css/nav.css',
                	'resources/css/students.css',
           	     	'resources/css/appeals.css',
                	'resources/css/homework.css',
                	'resources/css/calendar.css',
                	'resources/css/statistic.css',
                	'resources/css/method.css',
			'resources/css/auth.css',
                	'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
