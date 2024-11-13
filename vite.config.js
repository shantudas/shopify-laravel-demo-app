// import { defineConfig } from 'vite';
// import vue from '@vitejs/plugin-vue';
// import laravel from 'laravel-vite-plugin';
//
// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         vue(),
//     ],
// });
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    // plugins: [vue()],
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
});
