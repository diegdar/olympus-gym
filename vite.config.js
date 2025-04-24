import {
    defineConfig
} from 'vite';
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
            ],
            refresh: [`resources/views/**/*`],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
});