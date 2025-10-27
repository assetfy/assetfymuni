// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/calendar.js',
                'resources/js/sidebar.js',
                'resources/js/swal.js',
                'resources/js/carousel.js',
                'resources/js/swapy.js',
                'resources/js/chatbot.js',
                'resources/js/apexcharts.js',
                'resources/js/mapAndDonut.js',
                'resources/js/google.maps.js',
                'resources/js/customMap.js',
                'resources/js/map-iniModal.js',
            ],
            refresh: [
                'app/Livewire/**',
                'resources/views/**',
            ],
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: '18.231.5.13',
        },
        //https: {
         //   key: fs.readFileSync(path.resolve(__dirname, 'certs/private.pem')),
           // cert: fs.readFileSync(path.resolve(__dirname, 'certs/cloudflare.pem')),
        //},
    },
});
