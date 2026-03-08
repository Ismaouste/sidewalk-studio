import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useTheme';
import type { ConsentConfig } from '@/types';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Sidewalk Studio';
initializeTheme();

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        void import('@/lib/consent')
            .then(({ initializeConsent }) =>
                initializeConsent(
                    props.initialPage.props.consent as ConsentConfig,
                ),
            )
            .catch((error: unknown) => {
                const message =
                    error instanceof Error ? error.message : String(error);

                if (
                    message.includes('ERR_BLOCKED_BY_CLIENT') ||
                    message.includes(
                        'Failed to fetch dynamically imported module',
                    )
                ) {
                    return;
                }

                console.warn(
                    'Consent manager could not be loaded. Continuing without optional consent UI.',
                    error,
                );
            });
    },
    progress: {
        color: '#c97d0a',
    },
});
