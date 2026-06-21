import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const hmrHost = process.env.VITE_HMR_HOST || 'localhost';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true,
        hmr: {
            host: hmrHost,
        },
        watch: {
            ignored: (p) => p.includes('storage') || p.includes('bootstrap') || p.includes('node_modules'),
        },
    },
    esbuild: {
        target: 'es2022',
    },
    optimizeDeps: {
        esbuildOptions: {
            target: 'es2022',
        },
    },
    build: {
        target: 'es2022',
    },
});
