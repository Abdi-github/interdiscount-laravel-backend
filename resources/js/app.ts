import './bootstrap';
import './Styles/app.css';
import 'primeicons/primeicons.css';

import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import InterdiscountPreset from './Styles/theme';
import i18n from './i18n';
import { route } from 'ziggy-js';

window.route = route;

const appName = import.meta.env.VITE_APP_NAME || 'Interdiscount Admin';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.route = route;

        app.use(plugin)
            .use(pinia)
            .use(i18n)
            .use(PrimeVue, {
                theme: {
                    preset: InterdiscountPreset,
                    options: {
                        darkModeSelector: '.dark',
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .mount(el);
    },
    progress: {
        color: '#f5a623',
    },
});
