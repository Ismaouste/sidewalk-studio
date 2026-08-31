<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import { copy as copyTree } from '@/copy';
import { formatPublicDate } from '@/lib/formatDate';
import type { ContentItem, SiteProps } from '@/types';

defineProps<{
    items: ContentItem[];
    eyebrow: string;
    title: string;
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].layout.landmarks);
</script>

<template>
    <section
        v-if="items.length > 0"
        class="related-items-strip"
        :aria-label="copy.relatedItems"
    >
        <header class="related-items-strip__header">
            <p class="type-eyebrow">{{ eyebrow }}</p>
            <h2 class="type-h3 related-items-strip__title">{{ title }}</h2>
        </header>

        <ul class="related-items-strip__list">
            <li
                v-for="item in items"
                :key="item.slug"
                class="related-items-strip__item"
            >
                <Link :href="item.url" class="related-items-strip__link">
                    <ContentVisual :item="item" compact />
                    <div class="related-items-strip__body">
                        <p class="type-meta related-items-strip__date">
                            {{
                                formatPublicDate(
                                    item.published_at,
                                    page.props.site.locale,
                                    'month',
                                )
                            }}
                        </p>
                        <h3 class="type-h4 related-items-strip__item-title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body-sm related-items-strip__summary">
                            {{ item.summary }}
                        </p>
                    </div>
                </Link>
            </li>
        </ul>
    </section>
</template>

<style scoped>
.related-items-strip {
    display: grid;
    gap: var(--sw-space-sm);
    padding-top: var(--sw-space-sm);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
    min-width: 0;
    max-width: 100%;
}

.related-items-strip__header {
    display: grid;
    gap: var(--sw-space-3xs);
}

.related-items-strip__title {
    margin: 0;
    color: var(--sw-text-primary);
    overflow-wrap: anywhere;
    word-break: break-word;
}

.related-items-strip__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.related-items-strip__item {
    margin: 0;
    min-width: 0;
}

.related-items-strip__link {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-xs);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    color: inherit;
    text-decoration: none;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
    min-width: 0;
    max-width: 100%;
}

.related-items-strip__body {
    display: grid;
    gap: var(--sw-space-3xs);
    min-width: 0;
}

.related-items-strip__date {
    margin: 0;
    color: var(--sw-text-muted);
}

.related-items-strip__item-title {
    margin: 0;
    color: var(--sw-text-primary);
    overflow-wrap: anywhere;
    word-break: break-word;
}

.related-items-strip__summary {
    margin: 0;
    color: var(--sw-text-secondary);
    overflow-wrap: anywhere;
    word-break: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-items-strip__link:focus-visible {
    outline: none;
    border-color: var(--sw-border-focus);
}

.related-items-strip__link:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .related-items-strip__link:hover {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
    }
}

@media (min-width: 720px) {
    .related-items-strip__list {
        grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr));
        gap: var(--sw-space-sm);
    }

    .related-items-strip__link :deep(.content-visual) {
        min-height: 6rem;
    }
}
</style>
