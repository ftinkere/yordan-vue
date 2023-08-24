import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.es.js';
import route from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => title ? `${title} - ${appName}` : `${appName}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('../../resources/js/Pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const ziggy = {
                ...page.props.ziggy,
                location: new URL(page.props.ziggy.location),
            };
            global.route = (name, params, absolute, config = ziggy) => route(name, params, absolute, config);
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, ziggy);
        },
    })
);
