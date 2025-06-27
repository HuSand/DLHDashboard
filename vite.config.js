import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  server: {
    host: '127.0.0.1',   // biar nggak ke IPv6
    port: 5173,
    strictPort: true,    // error kalau port kepake, nggak auto ganti
  },
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
