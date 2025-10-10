import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/tableList.css',
                'resources/js/app.js',
                'resources/js/navbar.js',
                'resources/js/passwordVisibility.js',
                'resources/js/hideTableHeaders.js',
                'resources/js/messageTransition.js',
                'resources/js/admin/subscriptions/stats.js',
                'resources/js/activity-schedule/show.js',
                'resources/js/dashboard/memberCharts.js'
            ],
            refresh: [`resources/views/**/*`, `resources/css/**/*`, `resources/js/**/*`],  // Expanded patterns to force reload on changes in views, CSS, and JS
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',  // Listen on all interfaces for Docker/host access
        port: 5173,  // Explicitly set the port
        hmr: {
            host: 'localhost',  // HMR host for WebSocket connections
            port: 5173,  // HMR port
        },
        watch: {
            usePolling: true,  // Enable polling for reliable file watching in Docker
            interval: 500,  // Reduce polling interval for faster detection (adjust 300-1000 ms as needed)
        },
        cors: true,
    },
    css: {
        devSourcemap: true,  // Enable source maps for better CSS debugging
    },
});
