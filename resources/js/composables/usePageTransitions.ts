import { router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { clearStaticPreviewNavigation } from '@/lib/staticPreview';

const SHOW_DELAY_MS = 180;
const MIN_OVERLAY_MS = 320;
const SETTLING_MS = 120;
const MAX_OVERLAY_MS = 2200;

const state = reactive({
    isLoading: false,
    isSettling: false,
    isReady: true,
});

let listenersInstalled = false;
let staticPreviewListenersInstalled = false;
let transitionMode: 'unset' | 'inertia' | 'static' = 'unset';
let lastStart = 0;
let shownAt = 0;
let showTimer: number | undefined;
let finishTimer: number | undefined;
let settlingTimer: number | undefined;
let safetyTimer: number | undefined;

function clearTimers() {
    if (showTimer !== undefined) {
        window.clearTimeout(showTimer);
        showTimer = undefined;
    }

    if (finishTimer !== undefined) {
        window.clearTimeout(finishTimer);
        finishTimer = undefined;
    }

    if (settlingTimer !== undefined) {
        window.clearTimeout(settlingTimer);
        settlingTimer = undefined;
    }

    if (safetyTimer !== undefined) {
        window.clearTimeout(safetyTimer);
        safetyTimer = undefined;
    }
}

function releaseOverlay() {
    clearTimers();
    state.isLoading = false;
    state.isSettling = true;
    state.isReady = true;
    shownAt = 0;
    clearStaticPreviewNavigation();
    window.dispatchEvent(new CustomEvent('sidewalk:navigation-settle'));

    settlingTimer = window.setTimeout(() => {
        state.isSettling = false;
    }, SETTLING_MS);
}

function settleAfter(delay: number) {
    finishTimer = window.setTimeout(() => {
        releaseOverlay();
    }, delay);
}

function startTransition() {
    clearTimers();
    lastStart = window.performance.now();
    state.isSettling = false;
    state.isLoading = false;
    window.dispatchEvent(new CustomEvent('sidewalk:navigation-start'));

    showTimer = window.setTimeout(() => {
        state.isLoading = true;
        shownAt = window.performance.now();
        safetyTimer = window.setTimeout(() => {
            releaseOverlay();
        }, MAX_OVERLAY_MS);
    }, SHOW_DELAY_MS);
}

function startTransitionImmediately() {
    clearTimers();
    lastStart = window.performance.now();
    state.isSettling = false;
    state.isLoading = true;
    state.isReady = true;
    shownAt = window.performance.now();
    safetyTimer = window.setTimeout(() => {
        releaseOverlay();
    }, MAX_OVERLAY_MS);
    window.dispatchEvent(new CustomEvent('sidewalk:navigation-start'));
}

function finishTransition() {
    if (!state.isLoading) {
        clearTimers();
        state.isSettling = false;
        state.isReady = true;
        return;
    }

    const elapsed = window.performance.now() - shownAt;
    settleAfter(Math.max(0, MIN_OVERLAY_MS - elapsed));
}

function installTransitionListeners() {
    if (listenersInstalled || typeof window === 'undefined') {
        return;
    }

    listenersInstalled = true;
    lastStart = window.performance.now();

    router.on('start', () => {
        startTransition();
    });

    router.on('finish', () => {
        finishTransition();
    });

    router.on('invalid', () => {
        finishTransition();
    });

    router.on('exception', () => {
        finishTransition();
    });
}

function installStaticPreviewTransitionListeners() {
    if (staticPreviewListenersInstalled || typeof window === 'undefined') {
        return;
    }

    staticPreviewListenersInstalled = true;

    window.addEventListener('sidewalk:static-preview-start', () => {
        startTransition();
    });

    window.addEventListener('sidewalk:static-preview-arrive', (event) => {
        const detail =
            event instanceof CustomEvent &&
            event.detail &&
            typeof event.detail.startedAt === 'number'
                ? event.detail
                : null;

        if (!detail) {
            clearStaticPreviewNavigation();

            return;
        }

        const elapsed = Date.now() - detail.startedAt;

        if (elapsed < SHOW_DELAY_MS) {
            clearStaticPreviewNavigation();

            return;
        }

        startTransitionImmediately();
        settleAfter(Math.max(MIN_OVERLAY_MS, 520 - elapsed));
    });
}

export function configurePageTransitions(options?: { staticPreview?: boolean }) {
    if (options?.staticPreview) {
        transitionMode = 'static';
        installStaticPreviewTransitionListeners();

        return;
    }

    if (transitionMode === 'unset') {
        transitionMode = 'inertia';
    }

    installTransitionListeners();
}

export function usePageTransitions() {
    return {
        isLoading: computed(() => state.isLoading),
        isSettling: computed(() => state.isSettling),
        isReady: computed(() => state.isReady),
        showOverlay: computed(() => state.isLoading),
        dismissOverlay: releaseOverlay,
    };
}
