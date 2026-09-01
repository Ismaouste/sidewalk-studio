import { router } from '@inertiajs/vue3';
import { readStorage, removeStorage, writeStorage } from '@/lib/safeStorage';

/**
 * Consent tier T1: a cookieless, first-party page-count ping.
 *
 * It stores nothing about the visitor. The only browser state involved is
 * the opt-out itself, and Global Privacy Control is honored both here and
 * on the server, so a ping from a GPC browser is discarded twice.
 */

const OPT_OUT_KEY = 'sidewalk:audience-opt-out';

let initialized = false;
let endpoint = '/audience';
let lastPath: string | null = null;

export function isAudienceOptedOut(): boolean {
    return readStorage('local', OPT_OUT_KEY) === '1';
}

export function setAudienceOptOut(optedOut: boolean): void {
    if (optedOut) {
        writeStorage('local', OPT_OUT_KEY, '1');
    } else {
        removeStorage('local', OPT_OUT_KEY);
    }
}

function ping(path: string): void {
    if (
        isAudienceOptedOut() ||
        navigator.globalPrivacyControl === true ||
        path === lastPath
    ) {
        return;
    }

    lastPath = path;

    const body = new Blob(
        [
            JSON.stringify({
                path,
                locale: document.documentElement.lang === 'fr' ? 'fr' : 'en',
                referrer: document.referrer || null,
            }),
        ],
        { type: 'application/json' },
    );

    if (!navigator.sendBeacon(endpoint, body)) {
        void fetch(endpoint, { method: 'POST', body, keepalive: true }).catch(
            () => {
                /* a lost ping is a rounding error */
            },
        );
    }
}

export function initializeAudience(options: {
    enabled: boolean;
    endpoint: string;
}): void {
    if (initialized || !options.enabled || typeof window === 'undefined') {
        return;
    }

    initialized = true;
    endpoint = options.endpoint;

    ping(window.location.pathname);

    // `lastPath` dedupes the initial navigate event this also fires for,
    // and the second visit cycle a resting pointer's prefetch can cause.
    router.on('navigate', (event) => {
        ping(new URL(event.detail.page.url, window.location.origin).pathname);
    });
}
