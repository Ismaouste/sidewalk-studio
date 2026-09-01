<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { useLocalMemory } from '@/composables/useLocalMemory';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    /** Keys the remembered position. Without it nothing is remembered. */
    slug?: string;
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].layout.reading);

const { resumableRatio, rememberReadingPosition } = useLocalMemory();

const offeredRatio = ref<number | null>(null);
const offeredPercent = computed(() =>
    Math.round((offeredRatio.value ?? 0) * 100),
);

function scrollableHeight(): number {
    return Math.max(
        1,
        document.documentElement.scrollHeight - window.innerHeight,
    );
}

function prefersLessMotion(): boolean {
    return (
        document.documentElement.dataset.motion === 'reduced' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    );
}

/**
 * Sampled when the reader leaves rather than as they scroll. A position is
 * only ever needed at the end of a reading session, so there is no reason to
 * watch one being reached — which is also how this stays free of the scroll
 * listener the progress rail already does without.
 */
function remember(): void {
    if (!props.slug) {
        return;
    }

    rememberReadingPosition(props.slug, window.scrollY / scrollableHeight());
}

/** Leaving the tab counts as leaving; coming back to it does not. */
function rememberOnHide(): void {
    if (document.visibilityState === 'hidden') {
        remember();
    }
}

async function resume(): Promise<void> {
    const ratio = offeredRatio.value;
    offeredRatio.value = null;

    if (ratio === null) {
        return;
    }

    // The offer is part of the page, so removing it shortens the document.
    // Measuring before that settles lands the reader a little past where they
    // actually were.
    await nextTick();

    const top = ratio * scrollableHeight();
    const jump = () => window.scrollTo({ top, behavior: 'instant' });

    // A view transition turns the jump into a crossfade between the two
    // scroll positions. Smooth scrolling would instead drag the reader through
    // everything in between, which is the opposite of returning them to
    // where they were.
    if (prefersLessMotion() || !document.startViewTransition) {
        jump();

        return;
    }

    document.startViewTransition(jump);
}

onMounted(() => {
    // The offer is made once, on arrival, while the reader is still at the
    // top. It never scrolls on its own: being moved without asking undoes
    // whatever the reader was about to do.
    offeredRatio.value = props.slug ? resumableRatio(props.slug) : null;

    document.addEventListener('visibilitychange', rememberOnHide);
});

onBeforeUnmount(() => {
    document.removeEventListener('visibilitychange', rememberOnHide);
    remember();
});
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="seo" />

        <section class="article-show">
            <div class="article-show__progress" aria-hidden="true" />

            <header class="article-show__lead">
                <slot name="lead" />
            </header>

            <Panel
                v-if="offeredRatio !== null"
                as="aside"
                tone="elevated"
                class="article-show__resume"
            >
                <p class="article-show__resume-copy">
                    <span class="article-show__resume-title">
                        {{ copy.resumeTitle(offeredPercent) }}
                    </span>
                    <span class="article-show__resume-note">
                        {{ copy.resumeNote }}
                    </span>
                </p>

                <span class="article-show__resume-actions">
                    <Button size="sm" @click="resume">
                        {{ copy.resumeAction }}
                    </Button>
                    <Button
                        variant="ghost"
                        size="sm"
                        @click="offeredRatio = null"
                    >
                        {{ copy.resumeDismiss }}
                    </Button>
                </span>
            </Panel>

            <div class="article-show__layout">
                <Panel
                    as="article"
                    class="article-show__article"
                    tone="elevated"
                >
                    <slot name="article" />
                </Panel>

                <aside v-if="$slots.aside" class="article-show__aside">
                    <slot name="aside" />
                </aside>
            </div>

            <footer v-if="$slots.footer" class="article-show__footer">
                <slot name="footer" />
            </footer>
        </section>
    </SiteLayout>
</template>

<style scoped>
.article-show {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    max-width: 100%;
    overflow-x: clip;
}

.article-show__progress {
    position: sticky;
    top: 0;
    z-index: 1;
    height: 2px;
    margin-bottom: calc(-1 * var(--sw-space-sm));
    background: linear-gradient(
        90deg,
        var(--sw-accent-coral),
        var(--sw-accent-sun)
    );
    transform-origin: 0 50%;
    transform: scaleX(0);
    border-radius: 0 var(--sw-radius-pill) var(--sw-radius-pill) 0;
    animation: article-read-progress linear both;
    animation-timeline: scroll(root block);
    pointer-events: none;
}

@keyframes article-read-progress {
    to {
        transform: scaleX(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .article-show__progress {
        display: none;
    }
}

@supports not (animation-timeline: scroll()) {
    .article-show__progress {
        display: none;
    }
}

.article-show__lead {
    display: grid;
    gap: var(--sw-space-xs);
    max-width: 60rem;
    min-width: 0;
}

/* An offer, sitting in the flow where the reader already is on arrival — not
   an overlay, and never something that moves the page on its own. The frame,
   the blur and the border thickness come from Panel; only the layout and the
   accent edge are this component's business. */
.article-show__resume {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: var(--sw-space-2xs);
    max-width: 60rem;
    padding: var(--sw-space-2xs) var(--sw-space-xs);
    border-color: color-mix(
        in srgb,
        var(--sw-accent-dominant) 26%,
        var(--sw-border)
    );
}

.article-show__resume-copy {
    display: grid;
    gap: var(--sw-space-4xs);
    margin: 0;
    min-width: 0;
}

.article-show__resume-title {
    font-family: var(--sw-font-body);
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--sw-text-primary);
}

/* This sentence is the privacy disclosure, not decorative metadata, so it
   takes --sw-text-secondary. The muted token puts it near 2.3:1 at this
   size, which is not a contrast a reader should have to work at for the one
   line that says where their reading position is kept. */
.article-show__resume-note {
    font-size: 0.72rem;
    line-height: 1.35;
    color: var(--sw-text-secondary);
}

.article-show__resume-actions {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--sw-space-3xs);
}

.article-show__footer {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    max-width: 100%;
    margin-top: var(--sw-space-sm);
}

.article-show__layout {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
    min-width: 0;
}

.article-show__article {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    max-width: 100%;
    overflow: clip;
    padding: clamp(1.5rem, 3vw, 2.5rem);
}

.article-show__article > :deep(*) {
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
}

.article-show__article :deep(img),
.article-show__article :deep(svg),
.article-show__article :deep(video),
.article-show__article :deep(iframe),
.article-show__article :deep(pre),
.article-show__article :deep(table) {
    max-width: 100%;
}

.article-show__aside {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    max-width: 100%;
}

.article-show__aside > :deep(*) {
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
}

@media (min-width: 1040px) {
    .article-show__layout {
        grid-template-columns: minmax(0, 1fr) minmax(18rem, 21rem);
    }
}

@media (max-width: 640px) {
    .article-show__article {
        padding: var(--sw-space-xs);
    }
}
</style>
