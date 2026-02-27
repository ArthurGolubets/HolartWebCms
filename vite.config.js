import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [vue()],
    build: {
        outDir: 'resources/dist',
        manifest: true,
        rollupOptions: {
            input: resolve(__dirname, 'resources/js/app.js'),
        },
    },
});
