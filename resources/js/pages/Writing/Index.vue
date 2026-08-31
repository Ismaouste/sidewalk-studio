<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { formatPublicDate } from '@/lib/formatDate';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();

const page = usePage<{ site: SiteProps }>();

function writingMeta(item: ContentItem) {
    return [
        formatPublicDate(item.published_at, page.props.site.locale, 'month'),
        `${item.reading_time} min`,
    ];
}

const copy = computed(
    () => copyTree[page.props.site.locale].pages.writingIndex,
);

function entryLabel(item: ContentItem): string {
    return item.category === 'journal'
        ? copy.value.entryLabelJournal
        : copy.value.entryLabelNote;
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section writing-index">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.description"
            >
                <template #actions>
                    <Button href="/projects">{{ copy.projectsCta }}</Button>
                    <Button href="/contact" variant="secondary">
                        {{ copy.contactCta }}
                    </Button>
                </template>

                <LegendChip :label="copy.editorialLabel" tone="violet" />
                <LegendChip
                    :label="copy.publishedEntriesLabel(props.items.length)"
                    tone="sun"
                />
            </SectionIntro>

            <div class="writing-index__list">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    prefetch="hover"
                    cache-for="30s"
                    class="writing-index__link"
                >
                    <Panel class="writing-index__card" tone="surface">
                        <ContentVisual :item="item" compact />
                        <div class="writing-index__body">
                            <div class="writing-index__meta">
                                <LegendChip
                                    :label="entryLabel(item)"
                                    :tone="
                                        item.category === 'journal'
                                            ? 'violet'
                                            : 'coral'
                                    "
                                />
                                <span
                                    v-for="value in writingMeta(item)"
                                    :key="value"
                                    class="type-meta writing-index__meta-item"
                                >
                                    {{ value }}
                                </span>
                            </div>

                            <h2 class="type-h2 writing-index__title">
                                {{ item.title }}
                            </h2>

                            <p class="type-body writing-index__summary">
                                {{ item.summary }}
                            </p>

                            <div class="writing-index__tags">
                                <span
                                    v-for="tag in item.tags"
                                    :key="tag"
                                    class="type-meta writing-index__tag"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </Panel>
                </Link>
            </div>

            <nav class="writing-index__nudge" aria-label="Next step">
                <Button href="/contact" variant="ghost" arrow>
                    {{ copy.nudgeContactCta }}
                </Button>
            </nav>
        </section>
    </SiteLayout>
</template>

<style scoped>
.writing-index {
    display: grid;
    gap: var(--sw-space-sm);
}

.writing-index__list {
    display: grid;
    gap: var(--sw-space-sm);
    max-width: 60rem;
}

.writing-index__link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.writing-index__card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: clamp(12px, 2vw, 16px);
    align-items: stretch;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.writing-index__body {
    display: grid;
    gap: var(--sw-space-xs);
    align-content: start;
}

.writing-index__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    align-items: baseline;
    justify-content: flex-start;
}

.writing-index__meta-item {
    display: inline-flex;
    align-items: baseline;
    color: var(--sw-text-muted);
}

.writing-index__meta-item::before {
    content: '/';
    margin-right: 0.55rem;
    color: color-mix(in srgb, var(--sw-text-muted) 82%, transparent);
}

.writing-index__title,
.writing-index__summary {
    margin: 0;
}

.writing-index__title {
    color: var(--sw-text-primary);
}

.writing-index__summary {
    max-width: 48rem;
    color: var(--sw-text-secondary);
}

.writing-index__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.writing-index__tag {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
}

.writing-index__link:focus-visible {
    outline: none;
}

.writing-index__link:focus-visible .writing-index__card,
.writing-index__link:active .writing-index__card {
    border-color: var(--sw-border-focus);
}

.writing-index__link:active .writing-index__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .writing-index__link:hover .writing-index__card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        background: color-mix(in srgb, var(--sw-bg-elevated) 86%, transparent);
    }
}

@media (min-width: 720px) {
    .writing-index__card {
        grid-template-columns: minmax(5.8rem, 7rem) minmax(0, 1fr);
        column-gap: var(--sw-space-sm);
        row-gap: var(--sw-space-2xs);
    }

    .writing-index__card :deep(.content-visual) {
        min-height: 100%;
        height: 100%;
    }
}

.writing-index__nudge {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--sw-space-sm);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
}

@media (max-width: 640px) {
    .writing-index {
        gap: var(--sw-space-xs);
    }

    .writing-index__meta {
        align-items: start;
        justify-content: flex-start;
    }

    .writing-index__nudge {
        justify-content: stretch;
    }

    .writing-index__nudge :deep(.sw-button) {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
