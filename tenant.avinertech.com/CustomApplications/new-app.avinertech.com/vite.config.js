import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        vueJsx(),
    ],
    resolve: {
        alias: {
            'ziggy': '../../../vendor/tightenco/ziggy/dist/',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
            allOrigins: true,
        },
        cors: {
            origin: ['http://demo.avinertech.local', 'https://demo.avinertech.com'],
        },
        host: '0.0.0.0',
        strictPort: true,
    },
    build: {
        rollupOptions: {
            external: 'ziggy'
        }
    }
});
