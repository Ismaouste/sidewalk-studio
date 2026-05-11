<script setup lang="ts">
import SeoMeta from '@/components/SeoMeta.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

defineProps<{
    seo: SeoPayload;
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="seo" />

        <section class="article-show">
            <div class="article-show__progress" aria-hidden="true" />

            <header class="article-show__lead">
                <slot name="lead" />
            </header>

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
    border-radius: 0 999px 999px 0;
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
