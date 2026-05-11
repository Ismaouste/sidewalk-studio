<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import AmbientGrid from '@/components/design-system/AmbientGrid.vue';
import AppFooter from '@/components/layout/AppFooter.vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import BreadcrumbTrail from '@/components/layout/BreadcrumbTrail.vue';
import { usePageTransitions } from '@/composables/usePageTransitions';
import { useTheme } from '@/composables/useTheme';
import { loaderQuotes, type AppLoaderQuote } from '@/data/loaderQuotes';
import type { SeoPayload, SiteProps } from '@/types';

const page = usePage<{ seo?: SeoPayload; site: SiteProps }>();
const transitions = usePageTransitions();
const { isSettling } = transitions;
const { currentTheme } = useTheme();

const breadcrumbs = computed(() => page.props.seo?.breadcrumbs ?? []);
const LOADER_DISPLAY_MS = 980;
const LOADER_FADE_MS = 260;
const LOADER_QUOTES_TO_SHOW = 2;
const LOADER_SESSION_KEY = 'sidewalk-loader-seen';
const LOADER_REPLAY_EVENT = 'sidewalk:loader:replay';

const loaderVisible = ref(false);
const loaderQuoteIndex = ref(0);
const loaderSelection = ref<AppLoaderQuote[]>([]);
const loaderHasFinePointer = ref(false);
const loaderPointerActive = ref(false);
const loaderPointerX = ref(0);
const loaderPointerY = ref(0);
const currentLoaderQuote = computed(
    () => loaderSelection.value[loaderQuoteIndex.value] ?? null,
);

let loaderAdvanceTimer: number | undefined;
let loaderExitTimer: number | undefined;
let loaderReplayListener: ((event: Event) => void) | null = null;
let loaderPointerMoveListener: ((event: PointerEvent) => void) | null = null;
let loaderPointerLeaveListener: (() => void) | null = null;

function clearLoaderTimers(): void {
    if (loaderAdvanceTimer !== undefined) {
        window.clearTimeout(loaderAdvanceTimer);
        loaderAdvanceTimer = undefined;
    }

    if (loaderExitTimer !== undefined) {
        window.clearTimeout(loaderExitTimer);
        loaderExitTimer = undefined;
    }
}

function shuffleQuotes(items: AppLoaderQuote[]): AppLoaderQuote[] {
    return [...items].sort(() => Math.random() - 0.5);
}

function pickLoaderSelection(): AppLoaderQuote[] {
    const categories =
        currentTheme.value === 'sunset'
            ? ['sunset', 'lucid', 'humor']
            : ['morning', 'lucid', 'humor'];
    const primaryPool = loaderQuotes.filter((quote) =>
        categories.includes(quote.category),
    );
    const candidatePool =
        primaryPool.length >= LOADER_QUOTES_TO_SHOW
            ? primaryPool
            : loaderQuotes;

    return shuffleQuotes(candidatePool).slice(0, LOADER_QUOTES_TO_SHOW);
}

function startLoaderSequence(force = false): void {
    if (typeof window === 'undefined') {
        return;
    }

    loaderSelection.value = pickLoaderSelection();

    if (loaderSelection.value.length === 0) {
        return;
    }

    clearLoaderTimers();
    loaderQuoteIndex.value = 0;
    loaderVisible.value = true;
    loaderPointerActive.value = false;

    if (!force) {
        window.sessionStorage.setItem(LOADER_SESSION_KEY, '1');
    }

    advanceLoaderSequence();
}

function advanceLoaderSequence(): void {
    if (loaderQuoteIndex.value < loaderSelection.value.length - 1) {
        loaderAdvanceTimer = window.setTimeout(() => {
            loaderQuoteIndex.value += 1;
            advanceLoaderSequence();
        }, LOADER_DISPLAY_MS);

        return;
    }

    loaderExitTimer = window.setTimeout(
        () => {
            loaderVisible.value = false;
        },
        Math.max(LOADER_DISPLAY_MS - 350, LOADER_FADE_MS * 2),
    );
}

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    loaderHasFinePointer.value = window.matchMedia('(pointer:fine)').matches;

    loaderReplayListener = () => {
        startLoaderSequence(true);
    };

    loaderPointerMoveListener = (event: PointerEvent) => {
        if (!loaderVisible.value || !loaderHasFinePointer.value) {
            return;
        }

        loaderPointerX.value = event.clientX;
        loaderPointerY.value = event.clientY;
        loaderPointerActive.value = true;
    };

    loaderPointerLeaveListener = () => {
        loaderPointerActive.value = false;
    };

    window.addEventListener(LOADER_REPLAY_EVENT, loaderReplayListener);
    window.addEventListener('pointermove', loaderPointerMoveListener, {
        passive: true,
    });
    window.addEventListener('pointerleave', loaderPointerLeaveListener);

    const seen = window.sessionStorage.getItem(LOADER_SESSION_KEY) === '1';

    if (seen) {
        return;
    }

    startLoaderSequence();
});

onBeforeUnmount(() => {
    clearLoaderTimers();

    if (typeof window !== 'undefined') {
        if (loaderReplayListener !== null) {
            window.removeEventListener(
                LOADER_REPLAY_EVENT,
                loaderReplayListener,
            );
        }

        if (loaderPointerMoveListener !== null) {
            window.removeEventListener(
                'pointermove',
                loaderPointerMoveListener,
            );
        }

        if (loaderPointerLeaveListener !== null) {
            window.removeEventListener(
                'pointerleave',
                loaderPointerLeaveListener,
            );
        }
    }
});
</script>

<template>
    <div class="sw-shell">
        <AmbientGrid />
        <AppHeader />
        <transition name="app-loader-fade">
            <div
                v-if="loaderVisible"
                class="app-loader"
                aria-live="polite"
                aria-atomic="true"
            >
                <span
                    v-if="loaderHasFinePointer"
                    class="app-loader__cursor-halo"
                    :class="`app-loader__cursor-halo--${currentTheme}`"
                    :style="{
                        left: `${loaderPointerX}px`,
                        top: `${loaderPointerY}px`,
                        opacity: loaderPointerActive ? '1' : '0',
                    }"
                    aria-hidden="true"
                />
                <div class="app-loader__content">
                    <transition name="app-loader-quote" mode="out-in">
                        <div
                            v-if="currentLoaderQuote"
                            :key="`${loaderQuoteIndex}-${currentLoaderQuote.text}`"
                            class="app-loader__quote"
                        >
                            <p
                                class="app-loader__text"
                                :class="{
                                    'app-loader__text--mono':
                                        currentLoaderQuote.mono,
                                }"
                            >
                                « {{ currentLoaderQuote.text }} »
                            </p>
                            <p class="app-loader__author">
                                — {{ currentLoaderQuote.author }}
                            </p>
                        </div>
                    </transition>
                    <span
                        class="app-loader__rail"
                        :class="`app-loader__rail--${currentTheme}`"
                        aria-hidden="true"
                    />
                </div>
            </div>
        </transition>
        <main id="main-content" class="sw-main">
            <div class="sw-container">
                <BreadcrumbTrail
                    v-if="breadcrumbs.length"
                    :items="breadcrumbs"
                    class="sw-main__breadcrumb"
                />
                <div
                    class="sw-main__content"
                    :class="{
                        'sw-main__content--settling': isSettling,
                        'sw-main__content--with-breadcrumb':
                            breadcrumbs.length > 0,
                    }"
                >
                    <slot />
                </div>
            </div>
        </main>
        <AppFooter />
    </div>
</template>

<style scoped>
.app-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    pointer-events: none;
    overflow: hidden;
    padding-inline: clamp(1rem, 7vw, 6.5rem);
    background:
        radial-gradient(
            circle at 18% 18%,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 14%, transparent),
            transparent 34%
        ),
        radial-gradient(
            circle at 82% 22%,
            color-mix(in srgb, var(--sw-ambient-flare) 12%, transparent),
            transparent 36%
        ),
        radial-gradient(
            circle at 50% 72%,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 10%, transparent),
            transparent 42%
        ),
        color-mix(in srgb, var(--sw-bg-base) 34%, transparent);
    -webkit-backdrop-filter: blur(
            calc(var(--sw-runtime-surface-blur, 20px) * 1.2)
        )
        saturate(1.06);
    backdrop-filter: blur(calc(var(--sw-runtime-surface-blur, 20px) * 1.2))
        saturate(1.06);
    transition:
        opacity 0.48s ease,
        background-color 0.48s ease,
        -webkit-backdrop-filter 0.48s ease,
        backdrop-filter 0.48s ease;
}

.app-loader__content {
    position: relative;
    z-index: 1;
    display: grid;
    align-items: start;
    justify-items: start;
    gap: 0.75rem;
    width: min(34rem, calc(100vw - 2rem));
    padding-inline: 0;
    text-align: left;
}

.app-loader__cursor-halo {
    position: fixed;
    z-index: 0;
    width: clamp(7rem, 11vw, 10rem);
    aspect-ratio: 1;
    border-radius: 999px;
    pointer-events: none;
    transform: translate(-50%, -50%);
    filter: blur(18px);
    transition: opacity 160ms ease;
}

.app-loader__cursor-halo--morning {
    background: radial-gradient(
        circle,
        color-mix(in srgb, var(--sw-accent-green) 26%, transparent),
        color-mix(in srgb, var(--sw-accent-sun) 16%, transparent) 38%,
        transparent 72%
    );
}

.app-loader__cursor-halo--sunset {
    background: radial-gradient(
        circle,
        color-mix(in srgb, var(--sw-accent-violet) 30%, transparent),
        color-mix(in srgb, var(--sw-accent-coral) 18%, transparent) 36%,
        transparent 72%
    );
}

.app-loader__rail {
    position: relative;
    display: block;
    width: clamp(8.5rem, 22vw, 12rem);
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
    background: color-mix(in srgb, var(--sw-bg-elevated) 74%, transparent);
    border: 1px solid color-mix(in srgb, var(--sw-border) 44%, transparent);
    box-shadow:
        inset 0 0 0 1px color-mix(in srgb, white 8%, transparent),
        0 0 24px
            color-mix(in srgb, var(--app-loader-rail-glow) 34%, transparent);
}

.app-loader__rail--morning {
    --app-loader-rail-bright: color-mix(
        in srgb,
        var(--sw-accent-dominant) 78%,
        white 22%
    );
    --app-loader-rail-core: color-mix(
        in srgb,
        var(--sw-accent-green) 42%,
        var(--sw-accent-sun) 58%
    );
    --app-loader-rail-deep: color-mix(
        in srgb,
        var(--sw-accent-dominant) 84%,
        var(--sw-accent-coral) 16%
    );
    --app-loader-rail-glow: var(--sw-accent-green);
}

.app-loader__rail--sunset {
    --app-loader-rail-bright: color-mix(
        in srgb,
        var(--sw-accent-violet) 72%,
        white 28%
    );
    --app-loader-rail-core: color-mix(
        in srgb,
        var(--sw-accent-sun) 58%,
        var(--sw-accent-coral) 42%
    );
    --app-loader-rail-deep: color-mix(
        in srgb,
        var(--sw-accent-violet) 54%,
        var(--sw-accent-sun) 46%
    );
    --app-loader-rail-glow: var(--sw-accent-violet);
}

.app-loader__rail::before,
.app-loader__rail::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
}

.app-loader__rail::before {
    inset: 1px;
    background:
        radial-gradient(
            circle at 14% 50%,
            color-mix(in srgb, var(--app-loader-rail-bright) 96%, transparent),
            transparent 24%
        ),
        radial-gradient(
            circle at 52% 50%,
            color-mix(in srgb, var(--app-loader-rail-core) 94%, transparent),
            transparent 28%
        ),
        radial-gradient(
            circle at 86% 50%,
            color-mix(in srgb, var(--app-loader-rail-deep) 88%, transparent),
            transparent 24%
        ),
        linear-gradient(
            90deg,
            color-mix(in srgb, var(--app-loader-rail-deep) 74%, transparent),
            var(--app-loader-rail-core) 34%,
            var(--app-loader-rail-bright) 58%,
            color-mix(in srgb, var(--app-loader-rail-deep) 78%, transparent)
        );
    background-size:
        32% 180%,
        36% 180%,
        32% 180%,
        220% 100%;
    background-position:
        -12% 50%,
        50% 50%,
        112% 50%,
        130% 50%;
    filter: saturate(118%);
    animation:
        app-loader-rail-flow 3.4s linear infinite,
        app-loader-rail-pulse 2.4s ease-in-out infinite alternate;
}

.app-loader__rail::after {
    inset: -1px;
    background: linear-gradient(
        90deg,
        transparent 0%,
        transparent 28%,
        color-mix(in srgb, white 44%, var(--app-loader-rail-bright)) 46%,
        color-mix(in srgb, white 18%, transparent) 54%,
        transparent 72%,
        transparent 100%
    );
    mix-blend-mode: screen;
    opacity: 0.72;
    filter: blur(4px);
    transform: translateX(-132%);
    animation: app-loader-rail-sweep 2.8s ease-in-out infinite;
}

.app-loader__quote {
    display: grid;
    gap: 0.55rem;
}

.app-loader__text {
    margin: 0;
    font-size: 15px;
    font-style: italic;
    line-height: 1.55;
    max-width: 26rem;
    white-space: pre-line;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 86%,
        var(--sw-text-secondary)
    );
    text-wrap: pretty;
}

.app-loader__text--mono {
    font-family: var(--sw-font-code);
    font-size: 13px;
    font-style: normal;
}

.app-loader__author {
    margin: 0;
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.sw-main__breadcrumb {
    margin-bottom: clamp(2px, 0.5vw, 6px);
}

.sw-main__content {
    transition: opacity 140ms ease;
}

.sw-main__content--with-breadcrumb :deep(.sw-section:first-child) {
    padding-top: clamp(16px, 2.4vw, 28px);
}

.sw-main__content :deep(.sw-section--hero:first-child) {
    padding-top: clamp(44px, 8vw, 72px);
}

.sw-main__content--with-breadcrumb :deep(.sw-section--hero:first-child) {
    padding-top: clamp(16px, 2.4vw, 28px);
}

.sw-main__content--settling {
    opacity: 0.98;
}

.app-loader-fade-enter-active,
.app-loader-fade-leave-active {
    transition: opacity 0.48s ease;
}

.app-loader-fade-enter-from,
.app-loader-fade-leave-to {
    opacity: 0;
}

.app-loader-quote-enter-active,
.app-loader-quote-leave-active {
    transition: opacity 0.32s ease;
}

.app-loader-quote-enter-from,
.app-loader-quote-leave-to {
    opacity: 0;
}

@keyframes app-loader-rail-flow {
    0% {
        background-position:
            -12% 50%,
            50% 50%,
            112% 50%,
            130% 50%;
    }

    50% {
        background-position:
            8% 50%,
            60% 50%,
            92% 50%,
            20% 50%;
    }

    100% {
        background-position:
            24% 50%,
            76% 50%,
            76% 50%,
            -120% 50%;
    }
}

@keyframes app-loader-rail-pulse {
    from {
        opacity: 0.86;
        transform: scaleX(0.96);
    }

    to {
        opacity: 1;
        transform: scaleX(1);
    }
}

@keyframes app-loader-rail-sweep {
    0% {
        transform: translateX(-132%);
        opacity: 0;
    }

    12% {
        opacity: 0.6;
    }

    50% {
        transform: translateX(0%);
        opacity: 0.82;
    }

    88% {
        opacity: 0.38;
    }

    100% {
        transform: translateX(132%);
        opacity: 0;
    }
}

@media (max-width: 640px) {
    .app-loader {
        padding-inline: 1rem;
    }

    .app-loader__content {
        width: calc(100vw - 2rem);
        gap: 0.65rem;
    }

    .app-loader__rail {
        width: clamp(7rem, 30vw, 9rem);
    }
}

@media (prefers-reduced-motion: reduce) {
    .sw-main__content,
    .app-loader-fade-enter-active,
    .app-loader-fade-leave-active,
    .app-loader-quote-enter-active,
    .app-loader-quote-leave-active {
        transition: none;
    }

    .sw-main__content--settling {
        opacity: 1;
    }
}

:global(html[data-motion='reduced'] .sw-main__content),
:global(html[data-motion='reduced'] .app-loader-fade-enter-active),
:global(html[data-motion='reduced'] .app-loader-fade-leave-active),
:global(html[data-motion='reduced'] .app-loader-quote-enter-active),
:global(html[data-motion='reduced'] .app-loader-quote-leave-active) {
    transition: none;
}

:global(html[data-motion='reduced'] .sw-main__content--settling) {
    opacity: 1;
}
</style>
