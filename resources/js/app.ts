import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useTheme';
import { configurePageTransitions } from '@/composables/usePageTransitions';
import { initializeStaticPreviewNavigation } from '@/lib/staticPreview';
import type { ConsentConfig, SiteProps, ThemeSettings } from '@/types';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Sidewalk Studio';

function applyThemeSettings(settings?: ThemeSettings) {
    if (!settings || typeof document === 'undefined') {
        return;
    }

    document.documentElement.style.setProperty(
        '--sw-runtime-gradient-angle',
        `${settings.gradient_angle}deg`,
    );
    document.documentElement.style.setProperty(
        '--sw-runtime-surface-blur',
        `${settings.surface_blur}px`,
    );
    document.documentElement.style.setProperty(
        '--sw-runtime-line-thickness',
        `${settings.line_thickness}px`,
    );
}

function scheduleIdleTask(task: () => void) {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.requestIdleCallback) {
        window.requestIdleCallback(
            () => {
                task();
            },
            { timeout: 1200 },
        );

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
        initializeTheme(site.runtime.themeDefaults?.default_theme ?? null);
        applyThemeSettings(site.runtime.themeDefaults);

        configurePageTransitions({
            staticPreview: site.runtime.staticPreview,
        });

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        scheduleIdleTask(() => {
            void import('../css/fonts-deferred.css').catch(() => {
                /* deferred fonts are not critical; ignore failures */
            });
        });

        if (site.runtime.staticPreview) {
            initializeStaticPreviewNavigation(
                true,
                site.runtime.staticBasePath,
            );

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

        void import('@/lib/analytics-drivers').then(
            ({ setupAnalyticsAdapters }) => {
                const consent = props.initialPage.props
                    .consent as ConsentConfig;
                setupAnalyticsAdapters(consent.services.analytics);
            },
        );

        void import('@/lib/consent')
            .then(({ initializeConsent }) =>
                initializeConsent(
                    props.initialPage.props.consent as ConsentConfig,
                    site.locale,
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
