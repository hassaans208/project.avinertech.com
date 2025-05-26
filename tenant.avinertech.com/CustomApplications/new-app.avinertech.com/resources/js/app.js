import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy'
import { ZiggyVue } from 'ziggy-js'
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const route = Ziggy.routes;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(createPinia());
        app.use(plugin);
        app.use(ZiggyVue, Ziggy);
        // app.use(createZiggyVue());
        app.mount('#app');
        return app;
    },
    progress: {
        color: '#3B82F6',
    },
});