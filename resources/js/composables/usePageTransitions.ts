import { router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { clearStaticPreviewNavigation } from '@/lib/staticPreview';

/** How long the content stays dimmed after a visit lands. */
const SETTLING_MS = 120;

/**
 * A full-page reload in static preview has no Inertia lifecycle to hook, so
 * the settle is timed from when the click was announced instead.
 */
const STATIC_PREVIEW_SETTLE_MS = 520;

const state = reactive({
    isSettling: false,
});

let listenersInstalled = false;
let staticPreviewListenersInstalled = false;
let settlingTimer: number | undefined;

function settle(delay = 0): void {
    if (settlingTimer !== undefined) {
        window.clearTimeout(settlingTimer);
    }

    state.isSettling = true;
    clearStaticPreviewNavigation();

    settlingTimer = window.setTimeout(() => {
        state.isSettling = false;
        settlingTimer = undefined;
    }, SETTLING_MS + delay);
}

function installTransitionListeners() {
    if (listenersInstalled || typeof window === 'undefined') {
        return;
    }

    listenersInstalled = true;

    // Inertia 3 wraps the page swap in the view transition itself, which is
    // the only moment the DOM actually changes. This used to be hand-rolled
    // here: startViewTransition was called on `start` and handed a promise
    // that was not resolved until `finish`, so the browser held the old frame
    // for the whole request — hence the 2.2s escape hatch and the three
    // InvalidStateError catches that came with it. Setting the flag on the
    // pending visit hands all of that back to the framework.
    //
    // Setting it here rather than per-Link is what keeps it on visits no
    // component owns: router.visit calls, form submissions, redirects. It
    // does not reach browser back and forward, which emit no `before` — but
    // neither did the version this replaces, which hooked `start`.
    //
    // Reduced motion is deliberately not checked: view-transitions.css zeroes
    // the durations for both `prefers-reduced-motion` and the site's own
    // data-motion switch, so one rule covers both and this file does not have
    // to know about either.
    // Prefetch requests travel the same event pipeline as navigations but
    // never swap the page, so they must not start a view transition or a
    // settle: with `prefetch="hover"` a pointer resting on a link would
    // otherwise dim the content once for the prefetch and once for the click.
    //
    // `cancel`, `httpException` and `networkError` carry no visit in their
    // detail, so they cannot check the flag themselves — this count is how
    // they tell the two apart. It is a count and not a boolean because the
    // two overlap in exactly the case `prefetch="hover"` creates: a prefetch
    // cancelled while a real navigation is still in flight would clear a
    // boolean and settle early, ending the dim before the swap it belongs to.
    let navigationsInFlight = 0;

    router.on('before', (event) => {
        if (event.detail.visit.prefetch) {
            return;
        }

        navigationsInFlight += 1;
        event.detail.visit.viewTransition = true;
    });

    router.on('finish', (event) => {
        if (event.detail.visit.prefetch) {
            return;
        }

        navigationsInFlight = Math.max(0, navigationsInFlight - 1);
        settle();
    });

    // Renamed in Inertia 3: 'invalid' -> 'httpException' (a non-Inertia
    // response) and 'exception' -> 'networkError' (the request never landed).
    for (const event of ['cancel', 'httpException', 'networkError'] as const) {
        router.on(event, () => {
            if (navigationsInFlight === 0) {
                return;
            }

            navigationsInFlight -= 1;
            settle();
        });
    }
}

function installStaticPreviewTransitionListeners() {
    if (staticPreviewListenersInstalled || typeof window === 'undefined') {
        return;
    }

    staticPreviewListenersInstalled = true;

    window.addEventListener('sidewalk:static-preview-arrive', (event) => {
        const startedAt =
            event instanceof CustomEvent &&
            typeof event.detail?.startedAt === 'number'
                ? event.detail.startedAt
                : null;

        if (startedAt === null) {
            clearStaticPreviewNavigation();

            return;
        }

        settle(
            Math.max(0, STATIC_PREVIEW_SETTLE_MS - (Date.now() - startedAt)),
        );
    });
}

export function configurePageTransitions(options?: {
    staticPreview?: boolean;
}) {
    if (options?.staticPreview) {
        installStaticPreviewTransitionListeners();

        return;
    }

    installTransitionListeners();
}

export function usePageTransitions() {
    return {
        isSettling: computed(() => state.isSettling),
    };
}
