import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import vuetify from './vuetify';
import en from './i18n/en.json';
import jp from './i18n/jp.json';
import { createI18n } from 'vue-i18n';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const i18n = createI18n({
    locale: import.meta.env.VITE_APP_LOCALE,
    messages: {
        en,
        jp
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .use(vuetify)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
