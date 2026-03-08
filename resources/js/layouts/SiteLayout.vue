<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue';
import AmbientGrid from '@/components/design-system/AmbientGrid.vue';
import AppFooter from '@/components/layout/AppFooter.vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import BreadcrumbTrail from '@/components/layout/BreadcrumbTrail.vue';
import { usePageTransitions } from '@/composables/usePageTransitions';
import type { SeoPayload, SiteProps } from '@/types';

const page = usePage<{ seo?: SeoPayload; site: SiteProps }>();
const transitions = usePageTransitions();
const { isSettling, showOverlay } = transitions;

const breadcrumbs = computed(() => page.props.seo?.breadcrumbs ?? []);

type LoaderLine = {
    text: string;
    variant: 'message' | 'quote';
    author?: string;
};

const loaderLibrary = computed<LoaderLine[]>(() =>
    page.props.site.locale === 'fr'
        ? [
              {
                  text: 'Le prochain plan arrive.',
                  variant: 'message',
              },
              {
                  text: 'Je range deux props et je reviens.',
                  variant: 'message',
              },
              {
                  text: 'Une transition qui saute, c’est une discussion avec le GPU.',
                  variant: 'message',
              },
              {
                  text: 'Mode dev : un petit détour, pas une disparition.',
                  variant: 'message',
              },
              {
                  text: 'Il faut du temps pour voir ce qui tient.',
                  variant: 'quote',
                  author: 'René Char',
              },
              {
                  text: 'Le monde se refait toujours dans le détail.',
                  variant: 'quote',
              },
              {
                  text: 'Je suis faite de tous les mots que je ne peux pas prononcer.',
                  variant: 'quote',
                  author: 'Paul B. Preciado',
              },
              {
                  text: 'Le visible est toujours traversé par ce qui insiste en dessous.',
                  variant: 'quote',
              },
              {
                  text: 'Le prochain plan arrive avec un peu de drama contrôlé.',
                  variant: 'message',
              },
          ]
        : [
              {
                  text: 'The next view is arriving.',
                  variant: 'message',
              },
              {
                  text: 'I am rearranging two props and coming back.',
                  variant: 'message',
              },
              {
                  text: 'A jumpy transition is basically a conversation with the GPU.',
                  variant: 'message',
              },
              {
                  text: 'Dev mode: a short detour, not a disappearance.',
                  variant: 'message',
              },
              {
                  text: 'It takes time to see what really holds.',
                  variant: 'quote',
                  author: 'Rene Char',
              },
              {
                  text: 'The world is rebuilt inside the details.',
                  variant: 'quote',
              },
              {
                  text: 'I am made of all the words I cannot pronounce.',
                  variant: 'quote',
                  author: 'Paul B. Preciado',
              },
              {
                  text: 'The visible is always crossed by what insists underneath.',
                  variant: 'quote',
              },
              {
                  text: 'The next view arrives with a little controlled drama.',
                  variant: 'message',
              },
          ],
);

const currentLoaderLine = ref<LoaderLine | null>(null);
const overlayPointer = reactive({
    x: '50%',
    y: '38%',
    active: false,
});
let previousLoaderIndex = -1;
let localSafetyTimer: number | undefined;

function setScrollLock(enabled: boolean) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.toggleAttribute('data-scroll-lock', enabled);
    document.body.toggleAttribute('data-scroll-lock', enabled);
}

function pickLoaderLine() {
    const options = loaderLibrary.value;

    if (options.length === 0) {
        currentLoaderLine.value = null;
        return;
    }

    if (options.length === 1) {
        currentLoaderLine.value = options[0];
        previousLoaderIndex = 0;
        return;
    }

    let nextIndex = previousLoaderIndex;

    while (nextIndex === previousLoaderIndex) {
        nextIndex = Math.floor(Math.random() * options.length);
    }

    previousLoaderIndex = nextIndex;
    currentLoaderLine.value = options[nextIndex];
}

watch(
    showOverlay,
    (isVisible) => {
        if (localSafetyTimer !== undefined) {
            window.clearTimeout(localSafetyTimer);
            localSafetyTimer = undefined;
        }

        if (isVisible) {
            pickLoaderLine();
            setScrollLock(true);
            overlayPointer.active = false;

            localSafetyTimer = window.setTimeout(() => {
                transitions.dismissOverlay();
            }, 2600);

            return;
        }

        setScrollLock(false);
    },
    { immediate: true },
);

function handleOverlayPointerMove(event: PointerEvent) {
    const target = event.currentTarget;

    if (!(target instanceof HTMLElement)) {
        return;
    }

    const bounds = target.getBoundingClientRect();
    const x = ((event.clientX - bounds.left) / bounds.width) * 100;
    const y = ((event.clientY - bounds.top) / bounds.height) * 100;

    overlayPointer.x = `${Math.max(0, Math.min(100, x))}%`;
    overlayPointer.y = `${Math.max(0, Math.min(100, y))}%`;
    overlayPointer.active = true;
}

function resetOverlayPointer() {
    overlayPointer.active = false;
    overlayPointer.x = '50%';
    overlayPointer.y = '38%';
}

onBeforeUnmount(() => {
    if (localSafetyTimer !== undefined) {
        window.clearTimeout(localSafetyTimer);
    }

    setScrollLock(false);
});
</script>

<template>
    <div class="sw-shell">
        <AmbientGrid />
        <AppHeader />
        <transition name="sw-loader">
            <div
                v-if="showOverlay"
                class="sw-shell__loader"
                :class="{ 'sw-shell__loader--interactive': overlayPointer.active }"
                :style="{
                    '--sw-loader-hover-x': overlayPointer.x,
                    '--sw-loader-hover-y': overlayPointer.y,
                }"
                aria-live="polite"
                aria-atomic="true"
                @pointermove="handleOverlayPointerMove"
                @pointerleave="resetOverlayPointer"
                @wheel.prevent
                @touchmove.prevent
            >
                <div class="sw-shell__loader-copy">
                    <span class="type-meta sw-shell__loader-kicker">
                        {{
                            page.props.site.locale === 'fr'
                                ? 'Chargement'
                                : 'Loading'
                        }}
                    </span>
                    <strong class="sw-shell__loader-title">
                        <span
                            :class="{
                                'sw-shell__loader-title--quote':
                                    currentLoaderLine?.variant === 'quote',
                            }"
                        >
                            {{ currentLoaderLine?.text }}
                        </span>
                    </strong>
                    <cite
                        class="type-meta sw-shell__loader-author"
                        :class="{
                            'sw-shell__loader-author--visible':
                                !!currentLoaderLine?.author,
                        }"
                    >
                        {{ currentLoaderLine?.author ?? ' ' }}
                    </cite>
                    <span class="sw-shell__loader-line" />
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
.sw-shell__loader {
    position: fixed;
    inset: 0;
    z-index: var(--sw-z-overlay);
    display: grid;
    place-items: center;
    pointer-events: auto;
    touch-action: none;
    overflow: hidden;
    contain: layout paint style;
    background:
        radial-gradient(
            circle at var(--sw-loader-hover-x, 50%) var(--sw-loader-hover-y, 38%),
            color-mix(in srgb, var(--sw-ambient-flare-soft) 12%, transparent),
            transparent 22%
        ),
        radial-gradient(
            circle at var(--sw-sun-vx, 14%) var(--sw-sun-vy, 10%),
            color-mix(in srgb, var(--sw-ambient-flare) 14%, transparent),
            transparent 34%
        ),
        radial-gradient(
            circle at 78% 18%,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 7%, transparent),
            transparent 42%
        ),
        color-mix(
            in srgb,
            var(--sw-bg-base) 42%,
            var(--sw-ambient-flare-soft) 6%
        );
    backdrop-filter: blur(28px);
    transition:
        opacity var(--sw-motion-smooth),
        background var(--sw-motion-smooth),
        backdrop-filter var(--sw-motion-smooth);
}

.sw-shell__loader--interactive {
    background:
        radial-gradient(
            circle at var(--sw-loader-hover-x, 50%) var(--sw-loader-hover-y, 38%),
            color-mix(in srgb, var(--sw-ambient-flare-soft) 18%, transparent),
            transparent 22%
        ),
        radial-gradient(
            circle at var(--sw-sun-vx, 14%) var(--sw-sun-vy, 10%),
            color-mix(in srgb, var(--sw-ambient-flare) 18%, transparent),
            transparent 34%
        ),
        radial-gradient(
            circle at 78% 18%,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 13%, transparent),
            transparent 42%
        ),
        color-mix(
            in srgb,
            var(--sw-bg-base) 38%,
            var(--sw-ambient-flare-soft) 8%
        );
}

.sw-shell__loader-copy {
    display: grid;
    gap: 10px;
    width: min(30rem, calc(100vw - 2 * var(--sw-space-sm)));
    padding: clamp(18px, 2.8vw, 24px);
    border: 0;
    border-radius: calc(var(--sw-radius-lg) + 4px);
    background: transparent;
    box-shadow: none;
    justify-items: stretch;
    text-align: left;
    contain: layout paint style;
}

.sw-shell__loader-kicker {
    font-size: 0.72rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.sw-shell__loader-title {
    margin: 0;
    font-family: var(--sw-font-body);
    max-width: 24rem;
    min-height: calc(1.28em * 3);
    font-size: clamp(1.02rem, 2vw, 1.18rem);
    font-weight: 500;
    line-height: 1.28;
    color: color-mix(in srgb, var(--sw-text-secondary) 88%, var(--sw-text-primary));
    text-wrap: balance;
    transition: color var(--sw-motion-fast);
}

.sw-shell__loader-title--quote {
    font-family: var(--sw-font-body);
    font-style: italic;
    font-weight: 500;
    color: color-mix(in srgb, var(--sw-text-secondary) 94%, var(--sw-text-primary));
}

.sw-shell__loader--interactive .sw-shell__loader-title {
    color: color-mix(in srgb, var(--sw-text-secondary) 80%, var(--sw-text-primary));
}

.sw-shell__loader--interactive .sw-shell__loader-title--quote {
    color: color-mix(in srgb, var(--sw-text-secondary) 88%, var(--sw-text-primary));
}

.sw-shell__loader-author {
    min-height: 1rem;
    font-size: 0.7rem;
    font-style: normal;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: transparent;
}

.sw-shell__loader-author--visible {
    color: var(--sw-text-secondary);
}

.sw-shell__loader-line {
    position: relative;
    display: block;
    width: min(12rem, 42vw);
    height: 4px;
    border-radius: var(--sw-radius-full);
    overflow: hidden;
    background: color-mix(
        in srgb,
        var(--sw-bg-elevated) 84%,
        var(--sw-ambient-flare-soft) 16%
    );
    box-shadow:
        inset 0 0 0 1px color-mix(in srgb, var(--sw-border) 48%, transparent),
        0 0 22px color-mix(in srgb, var(--sw-ambient-flare) 10%, transparent);
}

.sw-shell__loader-line::before,
.sw-shell__loader-line::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
}

.sw-shell__loader-line::before {
    inset: 0.5px;
    background:
        radial-gradient(
            circle at 18% 50%,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 94%, transparent),
            transparent 26%
        ),
        radial-gradient(
            circle at 52% 50%,
            color-mix(in srgb, var(--sw-ambient-flare) 92%, transparent),
            transparent 32%
        ),
        radial-gradient(
            circle at 82% 50%,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 84%, transparent),
            transparent 28%
        ),
        linear-gradient(
            90deg,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 70%, transparent),
            color-mix(in srgb, var(--sw-ambient-flare) 92%, white 8%) 36%,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 94%, white 6%) 68%,
            color-mix(in srgb, var(--sw-ambient-flare-deep) 76%, transparent)
        );
    background-size:
        34% 170%,
        38% 200%,
        30% 170%,
        220% 100%;
    background-position:
        0% 50%,
        56% 50%,
        100% 50%,
        140% 50%;
    filter: saturate(115%);
    animation:
        sw-loader-flow 4.6s linear infinite,
        sw-loader-pulse 2.4s ease-in-out infinite alternate;
}

.sw-shell__loader-line::after {
    inset: -1px;
    background:
        linear-gradient(
            90deg,
            transparent 0%,
            transparent 28%,
            color-mix(in srgb, white 44%, var(--sw-ambient-flare-soft)) 46%,
            color-mix(in srgb, white 18%, transparent) 54%,
            transparent 70%,
            transparent 100%
        );
    mix-blend-mode: screen;
    opacity: 0.72;
    filter: blur(5px);
    transform: translateX(-132%);
    animation: sw-loader-sweep 2.8s ease-in-out infinite;
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

.sw-loader-enter-active,
.sw-loader-leave-active {
    transition: opacity var(--sw-motion-smooth);
}

.sw-loader-enter-from,
.sw-loader-leave-to {
    opacity: 0;
}

@keyframes sw-loader-flow {
    0% {
        background-position:
            -12% 46%,
            50% 54%,
            108% 48%,
            140% 50%;
    }

    50% {
        background-position:
            10% 54%,
            62% 44%,
            92% 56%,
            16% 50%;
    }

    100% {
        background-position:
            24% 48%,
            76% 52%,
            80% 44%,
            -112% 50%;
    }
}

@keyframes sw-loader-pulse {
    from {
        opacity: 0.84;
        transform: scaleY(0.96);
    }

    to {
        opacity: 1;
        transform: scaleY(1);
    }
}

@keyframes sw-loader-sweep {
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

@media (prefers-reduced-motion: reduce) {
    .sw-shell__loader-line::before,
    .sw-shell__loader-line::after {
        animation: none;
    }

    .sw-main__content,
    .sw-loader-enter-active,
    .sw-loader-leave-active {
        transition: none;
    }

    .sw-main__content--settling {
        opacity: 1;
    }
}

:global(html[data-motion='reduced'] .sw-shell__loader-line::before),
:global(html[data-motion='reduced'] .sw-shell__loader-line::after) {
    animation: none;
}

:global(html[data-motion='reduced'] .sw-main__content),
:global(html[data-motion='reduced'] .sw-loader-enter-active),
:global(html[data-motion='reduced'] .sw-loader-leave-active) {
    transition: none;
}

:global(html[data-motion='reduced'] .sw-main__content--settling) {
    opacity: 1;
}
</style>
