import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';
import { defineConfig } from 'vite';

const shouldGenerateWayfinder =
    process.env.VERCEL !== '1' &&
    process.env.VERCEL !== 'true' &&
    process.env.SKIP_WAYFINDER_GENERATE !== '1';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ...(shouldGenerateWayfinder
            ? [
                  // Vercel's Node build step has no PHP binary, so preview builds
                  // rely on the committed Wayfinder output instead of regenerating it.
                  wayfinder({
                      formVariants: true,
                  }),
              ]
            : []),
        ...(mode === 'analyze'
            ? [
                  visualizer({
                      filename: 'storage/app/vite-bundle-report.html',
                      gzipSize: true,
                      brotliSize: true,
                      open: false,
                  }),
              ]
            : []),
    ],
}));
