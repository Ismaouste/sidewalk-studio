<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();

function writingMeta(item: ContentItem) {
    return [
        {
            label: copy.value.publishedLabel,
            value: item.published_at,
        },
        {
            label: copy.value.readLabel,
            value: `${item.reading_time} min`,
        },
    ];
}

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              eyebrow: 'Archive des notes',
              title: "Notes de build, mémos de stratégie et décisions d'architecture.",
              description:
                  "Notes éditoriales sur les systèmes de contenu, l'orchestration du consentement, la préparation au SSR et la discipline de repo public.",
              projectsCta: 'Voir les références',
              contactCta: 'Prendre contact',
              editorialLabel: 'Journal éditorial',
              publishedEntriesLabel: `${props.items.length} notes publiées`,
              dividerLabel: 'Notes publiées',
              itemLabel: 'Note',
              publishedLabel: 'Publié',
              readLabel: 'Lecture',
          }
        : {
              eyebrow: 'Writing archive',
              title: 'Build notes, strategy memos, and architecture rationale.',
              description:
                  'Editorial notes on content systems, consent orchestration, SSR readiness, and public repo discipline.',
              projectsCta: 'Browse references',
              contactCta: 'Contact',
              editorialLabel: 'Editorial log',
              publishedEntriesLabel: `${props.items.length} published entries`,
              dividerLabel: 'Published entries',
              itemLabel: 'Writing',
              publishedLabel: 'Published',
              readLabel: 'Read',
          },
);
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
                <LegendChip :label="copy.publishedEntriesLabel" tone="sun" />
            </SectionIntro>

            <SectionDivider :label="copy.dividerLabel" />

            <div class="writing-index__list">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    class="writing-index__link"
                >
                    <Panel class="writing-index__card" tone="surface">
                        <ContentVisual :item="item" compact />
                        <div class="writing-index__card-top">
                            <LegendChip :label="copy.itemLabel" tone="violet" />
                            <ContentMetaRow :items="writingMeta(item)" />
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
                    </Panel>
                </Link>
            </div>
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
    position: relative;
    display: grid;
    gap: var(--sw-space-xs);
    overflow: hidden;
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.writing-index__card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 3px;
    transform: scaleX(0.24);
    transform-origin: left center;
    opacity: 0;
    background: linear-gradient(
        90deg,
        var(--sw-accent-violet),
        color-mix(in srgb, var(--sw-accent-sun) 50%, var(--sw-accent-violet))
    );
    transition:
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
}

.writing-index__card-top {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    align-items: center;
    justify-content: space-between;
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
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.writing-index__link:focus-visible {
    outline: none;
}

.writing-index__link:focus-visible .writing-index__card,
.writing-index__link:active .writing-index__card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.writing-index__link:focus-visible .writing-index__card::before,
.writing-index__link:active .writing-index__card::before {
    transform: scaleX(1);
    opacity: 1;
}

.writing-index__link:active .writing-index__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .writing-index__link:hover .writing-index__card {
        transform: translateY(-2px);
        border-color: var(--sw-accent-violet);
        background: color-mix(in srgb, var(--sw-bg-elevated) 86%, transparent);
        box-shadow: var(--sw-shadow-md);
    }

    .writing-index__link:hover .writing-index__card::before {
        transform: scaleX(1);
        opacity: 1;
    }
}

@media (max-width: 640px) {
    .writing-index {
        gap: var(--sw-space-xs);
    }

    .writing-index__card-top {
        align-items: start;
        justify-content: flex-start;
    }
}
</style>
