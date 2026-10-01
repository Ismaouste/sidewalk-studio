/**
 * The server-side render as a function, for a serverless Node function
 * (`api/ssr/render.mjs`): it takes the Inertia page object and returns the
 * `{ head, body }` the PHP gateway puts in the document. `ssr.ts` is the same
 * render as a long-running server, for a machine that can keep one running; Vercel
 * cannot, so the function is what production uses.
 *
 * Only the page is rendered here. The Stage and the sheet frame that
 * `app.ts` mounts beside it are decoration drawn in the browser; the browser
 * mounts the whole app over this markup, so what a crawler or a reader without
 * JavaScript gets is the page itself: its headings, its text and its links.
 */
import type { Page } from '@inertiajs/core';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

function resolvePage(name: string) {
    const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');

    return resolvePageComponent<DefineComponent>(`./pages/${name}.vue`, pages);
}

export default function render(page: Page) {
    return createInertiaApp({
        page,
        render: renderToString,
        resolve: resolvePage,
        setup: ({ App, props, plugin }) =>
            createSSRApp({ render: () => h(App, props) }).use(plugin),
    });
}
