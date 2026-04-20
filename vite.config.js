import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [ 'resources/js/app.js',
                //admin js
                                    //Module1
                'resources/js/admin/Module1/job-market-demands-form.js',
                'resources/js/admin/Module1/template-editor.js',
                                    //Module2
                'resources/js/admin/Module2/job-title-form.js',
                'resources/js/admin/Module2/lmi-submissions.js',
                
                                    //Module3
                'resources/js/admin/Module3/enrollment-form.js',
                'resources/js/admin/Module3/graduate-form.js',
                'resources/js/admin/Module3/licensure-rates-form.js',
                'resources/js/admin/Module3/supply-side-editor.js',
                                    //Module4   
                'resources/js/admin/Module4/program-stories-editor.js',
                                    //Module5
                'resources/js/admin/Module5/peso-directory-editor.js',
                

                //statistician js
                'resources/js/statistician/job-title-pending.js',
                'resources/js/statistician/statistician-review.js',
                'resources/js/statistician/supply-side-editor.js',
                'resources/js/statistician/template-editor.js',

                //public js
                'resources/js/public/home.js',
                'resources/js/public/job-market-demands.js',
                'resources/js/public/peso-directory.js',
                'resources/js/public/program-stories.js',
                'resources/js/public/supply-side',
                
               'resources/css/app.css',
               'resources/js/bootstrap.js',
            
            ],
                
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