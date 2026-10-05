import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/front.css',
                'resources/js/front.js',
                'resources/css/developer.css',
                'resources/js/developer.js',
                // Hanya dimuat oleh halaman proyek — lihat @push('page-scripts').
                // Hanya dimuat oleh halaman login.
                'resources/css/auth.css',
                'resources/js/auth.js',
                // Hanya dimuat sekali setelah login (?welcome=1).
                'resources/css/reveal.css',
                'resources/js/reveal.js',
                // Panel pratinjau (admin) dan penerimanya (halaman depan, ?pf_preview=1).
                'resources/css/preview.css',
                'resources/js/preview.js',
                'resources/js/preview-receiver.js',
                'resources/css/uploader.css',
                'resources/js/uploader.js',
            ],
            refresh: true,
        }),
    ],
});
