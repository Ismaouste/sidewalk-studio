import { router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

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

export function usePageTransitions() {
    installTransitionListeners();

    return {
        isLoading: computed(() => state.isLoading),
        isSettling: computed(() => state.isSettling),
        isReady: computed(() => state.isReady),
        showOverlay: computed(() => state.isLoading),
        dismissOverlay: releaseOverlay,
    };
}
