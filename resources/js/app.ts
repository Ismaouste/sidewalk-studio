import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useTheme';
import { usePageTransitions } from '@/composables/usePageTransitions';
import type { ConsentConfig } from '@/types';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Sidewalk Studio';
initializeTheme();
usePageTransitions();

function scheduleIdleTask(task: () => void) {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.requestIdleCallback) {
        window.requestIdleCallback(() => {
            task();
        }, { timeout: 1200 });

        return;
    }

    window.setTimeout(task, 1);
}

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

        scheduleIdleTask(() => {
            void import('@/lib/webVitals')
                .then(({ initializeWebVitals }) => {
                    initializeWebVitals();
                })
                .catch((error: unknown) => {
                    console.warn(
                        'Web Vitals instrumentation could not be initialized.',
                        error,
                    );
                });
        });

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
        color: '#8a7258',
    },
});
