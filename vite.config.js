import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [ 'resources/js/app.js',
                'resources/js/admin/enrollment-form.js',
                'resources/js/admin/graduate-form.js',],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
       /*host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            //host: '192.168.1.42', // 🔥 THIS IS THE KEY FIX
            host: '10.15.8.42',
            
        },*/
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});