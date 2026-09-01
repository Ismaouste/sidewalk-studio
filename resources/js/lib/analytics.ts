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

export function enableAnalytics(config: ConsentConfig): void {
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
    });
}
