import { router } from '@inertiajs/vue3';
import type posthogClient from 'posthog-js';
import { readStorage } from '@/lib/safeStorage';
import type { ConsentConfig } from '@/types';

/**
 * Consent tier T2 (product analytics) and the client half of T3 (replay).
 *
 * posthog-js enters the browser only through the dynamic import below,
 * which only runs once the `analytics` category has been accepted — the
 * library is absent from every bundle a non-consenting visitor loads.
 * Replay (T3) additionally requires its own stored opt-in and is never
 * started by "Accept all" alone.
 */

type PostHog = typeof posthogClient;

export const REPLAY_STORAGE_KEY = 'sidewalk:replay-opt-in';

let client: PostHog | null = null;
let loading: Promise<PostHog> | null = null;
let navigationHooked = false;

/**
 * Google Analytics 4 (Consent Mode v2, basic). Nothing here runs before the
 * `analytics` category is accepted: gtag.js is not in the page and no request
 * goes to Google until then. On a refusal, a measurement that was already
 * loaded is switched off with Google's own `ga-disable` flag and its cookies
 * are removed.
 */
const GA4_ID = /^G-[A-Z0-9]{6,12}$/;
let ga4Id: string | null = null;

type GtagWindow = Window & {
    dataLayer?: unknown[];
    gtag?: (...args: unknown[]) => void;
    [key: `ga-disable-${string}`]: boolean | undefined;
};

function enableGa4(config: ConsentConfig): void {
    const id = config.services.analytics.ga4?.id ?? null;

    if (!id || !GA4_ID.test(id)) {
        return;
    }

    const w = window as unknown as GtagWindow;
    w[`ga-disable-${id}`] = false;

    if (ga4Id === id) {
        return;
    }

    ga4Id = id;
    w.dataLayer = w.dataLayer ?? [];
    w.gtag = function gtag(...args: unknown[]) {
        w.dataLayer?.push(args);
    };
    w.gtag('consent', 'default', {
        ad_personalization: 'denied',
        ad_storage: 'denied',
        ad_user_data: 'denied',
        analytics_storage: 'granted',
    });
    w.gtag('js', new Date());
    w.gtag('config', id, { cookie_expires: 395 * 86400 });

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${id}`;
    document.head.appendChild(script);
    hookNavigation();
}

function disableGa4(): void {
    if (!ga4Id) {
        return;
    }

    (window as unknown as GtagWindow)[`ga-disable-${ga4Id}`] = true;

    for (const name of document.cookie
        .split(';')
        .map((c) => (c.split('=')[0] ?? '').trim())) {
        if (name === '_ga' || name.startsWith('_ga_')) {
            document.cookie = `${name}=; Max-Age=0; path=/`;
        }
    }
}

export function enableAnalytics(config: ConsentConfig): void {
    enableGa4(config);

    const posthogConfig = config.services.analytics.posthog;

    if (config.driver !== 'posthog' || !posthogConfig.key) {
        return;
    }

    const key = posthogConfig.key;

    loading ??= import('posthog-js').then(({ default: posthog }) => {
        posthog.init(key, {
            api_host: posthogConfig.host,
            autocapture: false,
            capture_pageview: false,
            disable_session_recording: true,
            person_profiles: 'identified_only',
            persistence: 'localStorage',
        });

        return posthog;
    });

    void loading.then((posthog) => {
        client = posthog;

        if (posthog.has_opted_out_capturing()) {
            posthog.opt_in_capturing();
        }

        posthog.capture('$pageview');
        hookNavigation();
        setSessionReplay(readStorage('local', REPLAY_STORAGE_KEY) === '1');
    });
}

export function disableAnalytics(): void {
    disableGa4();

    if (!client) {
        return;
    }

    client.stopSessionRecording();
    client.opt_out_capturing();
    client.reset();
}

export function capture(
    event: string,
    properties: Record<string, string> = {},
): void {
    client?.capture(event, properties);
}

export function setSessionReplay(enabled: boolean): void {
    if (!client || client.has_opted_out_capturing()) {
        return;
    }

    if (enabled) {
        client.startSessionRecording();
    } else {
        client.stopSessionRecording();
    }
}

function hookNavigation(): void {
    if (navigationHooked) {
        return;
    }

    navigationHooked = true;

    router.on('navigate', () => {
        client?.capture('$pageview');

        if (ga4Id) {
            (window as unknown as GtagWindow).gtag?.('config', ga4Id, {
                page_location: window.location.href,
                page_path: window.location.pathname,
            });
        }
    });
}
