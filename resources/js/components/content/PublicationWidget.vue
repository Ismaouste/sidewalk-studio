<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import type { PublicationWidget } from '@/types';

const page = usePage<{ site: { locale: string } }>();

const props = defineProps<{
    widget: PublicationWidget;
    tone?: 'surface' | 'grid';
}>();

const isFrench = computed(() => page.props.site.locale === 'fr');

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

function widgetChipLabel(section: string, category: string, client: string): string {
    if (section === 'case-studies') {
        return client || (isFrench.value ? 'Référence' : 'Reference');
    }

    return category === 'journal'
        ? 'Journal'
        : isFrench.value
          ? 'Note'
          : 'Note';
}
</script>

<template>
    <section class="publication-widget">
        <div class="publication-widget__header">
            <SectionIntro
                :eyebrow="props.widget.eyebrow"
                :title="props.widget.title"
                :description="props.widget.description"
            />
            <Button :href="props.widget.ctaHref" variant="ghost">
                {{ props.widget.ctaLabel }}
            </Button>
        </div>

        <div class="publication-widget__grid">
            <Link
                v-for="item in props.widget.items"
                :key="`${item.section}-${item.slug}`"
                :href="item.url"
                class="publication-widget__link"
            >
                <Panel class="publication-widget__card" :tone="props.tone ?? 'surface'">
                    <ContentVisual :item="item" compact />
                    <div class="publication-widget__body">
                        <div class="publication-widget__meta">
                            <LegendChip
                                :label="widgetChipLabel(item.section, item.category, item.client)"
                                :tone="widgetChipTone(item.accent_tone, item.section)"
                            />
                            <span class="type-meta">
                                {{ item.published_at }}
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
    </section>
</template>

<style scoped>
.publication-widget {
    display: grid;
    gap: var(--sw-space-sm);
}

.publication-widget__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
    align-items: end;
    justify-content: space-between;
}

.publication-widget__header :deep(.section-intro) {
    max-width: 42rem;
}

.publication-widget__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.publication-widget__link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.publication-widget__card {
    display: grid;
    gap: var(--sw-space-xs);
    height: 100%;
    padding: var(--sw-space-sm);
    align-items: start;
    contain: layout paint style;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.publication-widget__body {
    display: grid;
    gap: var(--sw-space-xs);
}

.publication-widget__meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: var(--sw-space-xs);
    align-items: center;
}

.publication-widget__title,
.publication-widget__summary {
    margin: 0;
}

.publication-widget__title {
    color: var(--sw-text-primary);
}

.publication-widget__summary {
    color: var(--sw-text-secondary);
}

.publication-widget__link:focus-visible {
    outline: none;
}

.publication-widget__link:focus-visible .publication-widget__card,
.publication-widget__link:active .publication-widget__card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.publication-widget__link:active .publication-widget__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .publication-widget__link:hover .publication-widget__card {
        transform: translateY(-2px);
        box-shadow: var(--sw-shadow-md);
    }
}

@media (max-width: 960px) {
    .publication-widget__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (min-width: 720px) and (max-width: 1100px) {
    .publication-widget__card {
        grid-template-columns: minmax(7.8rem, 9.4rem) minmax(0, 1fr);
        column-gap: var(--sw-space-sm);
        row-gap: var(--sw-space-2xs);
    }

    .publication-widget__card :deep(.content-visual) {
        min-height: 6.8rem;
    }

    .publication-widget__link:nth-child(even) .publication-widget__card {
        grid-template-columns: minmax(0, 1fr) minmax(7.8rem, 9.4rem);
    }

    .publication-widget__link:nth-child(even) .publication-widget__card
        :deep(.content-visual) {
        order: 2;
    }

    .publication-widget__link:nth-child(even) .publication-widget__body {
        order: 1;
    }
}
</style>
