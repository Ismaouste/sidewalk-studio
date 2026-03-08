import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useTheme';
import { configurePageTransitions } from '@/composables/usePageTransitions';
import { initializeStaticPreviewNavigation } from '@/lib/staticPreview';
import type { ConsentConfig, SiteProps } from '@/types';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Sidewalk Studio';
initializeTheme();

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
        const site = props.initialPage.props.site as SiteProps;

        configurePageTransitions({
            staticPreview: site.runtime.staticPreview,
        });

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        if (site.runtime.staticPreview) {
            initializeStaticPreviewNavigation(true);

            return;
        }

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

        scheduleIdleTask(() => {
            void import('@/lib/performanceAudit')
                .then(({ initializePerformanceAudit }) => {
                    initializePerformanceAudit();
                })
                .catch((error: unknown) => {
                    console.warn(
                        'Performance audit instrumentation could not be initialized.',
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
        color: 'var(--sw-accent-sun)',
    },
});
