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
const LOADER_DISPLAY_MS = 2500;
const LOADER_FADE_MS = 400;
const LOADER_QUOTES_TO_SHOW = 3;
const LOADER_SESSION_KEY = 'sidewalk-loader-seen';

const loaderVisible = ref(false);
const loaderQuoteIndex = ref(0);
const loaderSelection = ref<AppLoaderQuote[]>([]);
const currentLoaderQuote = computed(
    () => loaderSelection.value[loaderQuoteIndex.value] ?? null,
);

let loaderAdvanceTimer: number | undefined;
let loaderExitTimer: number | undefined;

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
        primaryPool.length >= LOADER_QUOTES_TO_SHOW ? primaryPool : loaderQuotes;

    return shuffleQuotes(candidatePool).slice(0, LOADER_QUOTES_TO_SHOW);
}

function advanceLoaderSequence(): void {
    if (loaderQuoteIndex.value < loaderSelection.value.length - 1) {
        loaderAdvanceTimer = window.setTimeout(() => {
            loaderQuoteIndex.value += 1;
            advanceLoaderSequence();
        }, LOADER_DISPLAY_MS);

        return;
    }

    loaderExitTimer = window.setTimeout(() => {
        loaderVisible.value = false;
    }, LOADER_DISPLAY_MS);
}

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    const seen = window.sessionStorage.getItem(LOADER_SESSION_KEY) === '1';

    if (seen) {
        return;
    }

    loaderSelection.value = pickLoaderSelection();

    if (loaderSelection.value.length === 0) {
        return;
    }

    loaderQuoteIndex.value = 0;
    loaderVisible.value = true;
    window.sessionStorage.setItem(LOADER_SESSION_KEY, '1');
    advanceLoaderSequence();
});

onBeforeUnmount(() => {
    clearLoaderTimers();
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
                </div>
            </div>
        </transition>
        <main class="sw-main">
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
    display: grid;
    place-items: center;
    pointer-events: none;
    background: color-mix(in srgb, var(--sw-bg-base) 96%, transparent);
    transition: opacity 0.6s ease;
}

.app-loader__content {
    display: grid;
    width: min(31.25rem, calc(100vw - 3rem));
    padding-inline: 1.5rem;
    text-align: center;
}

.app-loader__quote {
    display: grid;
    gap: 0.75rem;
}

.app-loader__text {
    margin: 0;
    font-size: 15px;
    font-style: italic;
    line-height: 1.6;
    color: var(--sw-text-secondary);
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
    transition: opacity 0.6s ease;
}

.app-loader-fade-enter-from,
.app-loader-fade-leave-to {
    opacity: 0;
}

.app-loader-quote-enter-active,
.app-loader-quote-leave-active {
    transition: opacity 0.4s ease;
}

.app-loader-quote-enter-from,
.app-loader-quote-leave-to {
    opacity: 0;
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
