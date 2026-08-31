<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import { formatPublicDate } from '@/lib/formatDate';
import type { PublicationWidget, SiteProps } from '@/types';

const props = defineProps<{
    widget: PublicationWidget;
    tone?: 'surface' | 'grid';
}>();

const page = usePage<{ site: SiteProps }>();

const copy = computed(
    () => copyTree[page.props.site.locale].content.publicationWidget,
);
const hasHeader = computed(
    () =>
        Boolean(props.widget.eyebrow) ||
        Boolean(props.widget.title) ||
        Boolean(props.widget.description),
);
const hasCta = computed(
    () => Boolean(props.widget.ctaLabel) && Boolean(props.widget.ctaHref),
);
const isSingleItem = computed(() => props.widget.items.length === 1);

function widgetChipTone(
    tone: string,
    section: string,
): 'dominant' | 'green' | 'sun' | 'coral' | 'violet' {
    if (
        tone === 'dominant' ||
        tone === 'green' ||
        tone === 'sun' ||
        tone === 'coral' ||
        tone === 'violet'
    ) {
        return tone;
    }

    return section === 'writing' ? 'violet' : 'green';
}

function widgetChipLabel(
    section: string,
    category: string,
    client: string,
): string {
    if (section === 'case-studies') {
        return client || copy.value.referenceLabel;
    }

    return category === 'journal'
        ? copy.value.journalLabel
        : copy.value.noteLabel;
}

function formattedDate(value: string): string {
    return formatPublicDate(value, page.props.site.locale, 'month');
}
</script>

<template>
    <section class="publication-widget">
        <div v-if="hasHeader" class="publication-widget__header">
            <SectionIntro
                v-if="hasHeader"
                :eyebrow="props.widget.eyebrow"
                :title="props.widget.title"
                :description="props.widget.description"
            />
        </div>

        <div
            class="publication-widget__grid"
            :class="{ 'publication-widget__grid--single': isSingleItem }"
        >
            <Link
                v-for="item in props.widget.items"
                :key="`${item.section}-${item.slug}`"
                :href="item.url"
                prefetch="hover"
                cache-for="30s"
                class="publication-widget__link"
                :class="{
                    'publication-widget__link--note':
                        item.section === 'writing' &&
                        item.category !== 'journal',
                }"
            >
                <Panel
                    class="publication-widget__card"
                    :class="{
                        'publication-widget__card--note':
                            item.section === 'writing' &&
                            item.category !== 'journal',
                    }"
                    :tone="props.tone ?? 'surface'"
                >
                    <ContentVisual :item="item" compact />
                    <div class="publication-widget__body">
                        <div class="publication-widget__meta">
                            <LegendChip
                                :label="
                                    widgetChipLabel(
                                        item.section,
                                        item.category,
                                        item.client,
                                    )
                                "
                                :tone="
                                    widgetChipTone(
                                        item.accent_tone,
                                        item.section,
                                    )
                                "
                                class="publication-widget__meta-chip"
                            />
                            <span
                                class="type-meta publication-widget__meta-date"
                            >
                                {{ formattedDate(item.published_at) }}
                            </span>
                        </div>

                        <h3 class="type-h2 publication-widget__title">
                            {{ item.title }}
                        </h3>

                        <p class="type-body publication-widget__summary">
                            {{ item.summary }}
                        </p>
                    </div>
                </Panel>
            </Link>
        </div>

        <div v-if="hasCta" class="publication-widget__footer">
            <Button :href="props.widget.ctaHref" variant="ghost" arrow>
                {{ props.widget.ctaLabel }}
            </Button>
        </div>
    </section>
</template>

<style scoped>
.publication-widget {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.publication-widget__header {
    display: grid;
    gap: var(--sw-space-xs);
}

.publication-widget__header :deep(.section-intro) {
    max-width: 42rem;
}

.publication-widget__footer {
    display: flex;
    justify-content: flex-start;
}

.publication-widget__footer :deep(.sw-button) {
    white-space: normal;
    max-width: 16rem;
    line-height: 1.3;
}

.publication-widget__grid {
    display: grid;
    gap: var(--sw-space-xs);
    grid-template-columns: repeat(2, minmax(0, 1fr));
    min-width: 0;
}

.publication-widget__link {
    display: block;
    border-radius: var(--sw-radius-lg);
    min-width: 0;
}

.publication-widget__card {
    display: grid;
    gap: 0.62rem;
    height: 100%;
    padding: clamp(11px, 1.4vw, 14px);
    align-items: stretch;
    contain: paint;
    min-width: 0;
    border-color: color-mix(in srgb, var(--sw-border) 80%, transparent);
    background: var(--sw-bg-surface);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.publication-widget__body {
    display: grid;
    gap: 0.58rem;
    align-content: start;
    min-width: 0;
}

.publication-widget__meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 0.45rem;
    align-items: baseline;
}

.publication-widget__title,
.publication-widget__summary {
    margin: 0;
}

.publication-widget__title {
    color: var(--sw-text-primary);
    line-height: 1.12;
    overflow-wrap: anywhere;
}

.publication-widget__summary {
    color: var(--sw-text-secondary);
    line-height: 1.45;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    overflow-wrap: break-word;
}

.publication-widget__meta-date {
    display: inline-flex;
    align-items: baseline;
    color: var(--sw-text-muted);
}

.publication-widget__meta-date::before {
    content: '/';
    margin-right: 0.55rem;
    color: color-mix(in srgb, var(--sw-text-muted) 82%, transparent);
}

.publication-widget__link:focus-visible {
    outline: none;
}

.publication-widget__link:focus-visible .publication-widget__card,
.publication-widget__link:active .publication-widget__card {
    border-color: var(--sw-border-focus);
}

.publication-widget__link:active .publication-widget__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .publication-widget__link:hover .publication-widget__card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
    }
}

@media (max-width: 960px) {
    .publication-widget__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (min-width: 720px) {
    .publication-widget__card {
        grid-template-columns: minmax(4.8rem, 6rem) minmax(0, 1fr);
        column-gap: var(--sw-space-xs);
        row-gap: 0.55rem;
    }

    .publication-widget__card :deep(.content-visual) {
        min-height: 100%;
        height: 100%;
    }
}

@media (min-width: 720px) {
    .publication-widget__grid--single {
        grid-template-columns: minmax(0, 1fr);
    }

    .publication-widget__grid--single .publication-widget__card {
        grid-template-columns: minmax(6.2rem, 7.4rem) minmax(0, 1fr);
        column-gap: var(--sw-space-xs);
        row-gap: 0.65rem;
        align-items: center;
    }

    .publication-widget__grid--single
        .publication-widget__card
        :deep(.content-visual) {
        min-height: 6.6rem;
    }
}

.publication-widget__card--note :deep(.content-visual) {
    min-height: 5.4rem;
}
</style>
