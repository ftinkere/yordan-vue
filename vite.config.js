import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        server: {
            hmr: {
                host: 'localhost',
            },
        },
        host: 'localhost',
        cors: '*',
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
            script: {
                defineModel: true
            }
        }),
    ],
    resolve: {
        alias: {
            ziggy: 'vendor/tightenco/ziggy/dist/vue.es.js',
        },
    },
    optimizeDeps: {
        exclude: [
            'verte'
        ]
    }
});
