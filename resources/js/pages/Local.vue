<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    nancy: {
        body: string[];
    };
    signals: string[];
    journalSection: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    journalItems: ContentItem[];
    engagementsIntro: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    engagements: Array<{
        title: string;
        summary: string;
        items: string[];
    }>;
    notesSection: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    notes: ContentItem[];
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Voir les références',
              contactCta: 'Prendre contact',
              heroChipBase: 'Grand Est',
              heroChipMobility: 'Mobilité',
              heroChipJournal: 'Journal',
              signalsLabel: 'Ce que je regarde',
              publicationLabel: 'Publication',
              noteLabel: 'Note',
              publishedLabel: 'Publié',
              readLabel: 'Lecture',
          }
        : {
              projectsCta: 'Browse references',
              contactCta: 'Contact',
              heroChipBase: 'Lorraine corridor',
              heroChipMobility: 'Mobility',
              heroChipJournal: 'Journal',
              signalsLabel: 'What I keep watching',
              publicationLabel: 'Entry',
              noteLabel: 'Note',
              publishedLabel: 'Published',
              readLabel: 'Read',
          },
);

function publicationMeta(item: ContentItem) {
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
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section local-page">
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <template #actions>
                    <Button href="/projects">{{ copy.projectsCta }}</Button>
                    <Button href="/contact" variant="secondary">
                        {{ copy.contactCta }}
                    </Button>
                </template>

                <LegendChip :label="copy.heroChipBase" tone="dominant" />
                <LegendChip :label="copy.heroChipMobility" tone="green" />
                <LegendChip :label="copy.heroChipJournal" tone="sun" />
            </SectionIntro>

            <div class="local-page__base">
                <Panel class="local-page__panel" tone="surface">
                    <div class="local-page__copy">
                        <p
                            v-for="paragraph in props.nancy.body"
                            :key="paragraph"
                            class="type-body local-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>
                    </div>
                </Panel>

                <Panel class="local-page__panel" tone="grid">
                    <p class="type-eyebrow">{{ copy.signalsLabel }}</p>
                    <ul class="local-page__signal-list">
                        <li
                            v-for="signal in props.signals"
                            :key="signal"
                            class="local-page__signal-item"
                        >
                            <p class="type-body local-page__copy-line">
                                {{ signal }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>

            <div class="local-page__section-head">
                <SectionIntro
                    :eyebrow="props.journalSection.eyebrow"
                    :title="props.journalSection.title"
                    :description="props.journalSection.summary"
                />
            </div>

            <div id="journal" class="local-page__rail">
                <Link
                    v-for="item in props.journalItems"
                    :key="item.slug"
                    :href="item.url"
                    class="local-page__rail-link"
                >
                    <Panel class="local-page__rail-card" tone="surface">
                        <ContentVisual :item="item" compact />
                        <div class="local-page__rail-top">
                            <LegendChip
                                :label="copy.publicationLabel"
                                tone="violet"
                            />
                            <ContentMetaRow :items="publicationMeta(item)" />
                        </div>
                        <h2 class="type-h2 local-page__title">
                            {{ item.title }}
                        </h2>
                        <p class="type-body local-page__copy-line">
                            {{ item.summary }}
                        </p>
                    </Panel>
                </Link>
            </div>

            <div class="local-page__section-head">
                <SectionIntro
                    :eyebrow="props.engagementsIntro.eyebrow"
                    :title="props.engagementsIntro.title"
                    :description="props.engagementsIntro.summary"
                />
            </div>

            <div class="local-page__engagements">
                <article
                    v-for="engagement in props.engagements"
                    :key="engagement.title"
                    class="local-page__engagement"
                >
                    <h2 class="type-h2 local-page__title">
                        {{ engagement.title }}
                    </h2>
                    <p class="type-body local-page__copy-line">
                        {{ engagement.summary }}
                    </p>

                    <ul
                        v-if="engagement.items.length"
                        class="local-page__engagement-list"
                    >
                        <li
                            v-for="item in engagement.items"
                            :key="item"
                            class="local-page__engagement-item"
                        >
                            <p class="type-body local-page__copy-line">
                                {{ item }}
                            </p>
                        </li>
                    </ul>
                </article>
            </div>

            <div class="local-page__section-head">
                <SectionIntro
                    :eyebrow="props.notesSection.eyebrow"
                    :title="props.notesSection.title"
                    :description="props.notesSection.summary"
                />
            </div>

            <div id="notes" class="local-page__notes">
                <Link
                    v-for="item in props.notes"
                    :key="item.slug"
                    :href="item.url"
                    class="local-page__note-link"
                >
                    <article class="local-page__note">
                        <div class="local-page__note-top">
                            <LegendChip :label="copy.noteLabel" tone="violet" />
                            <ContentMetaRow :items="publicationMeta(item)" />
                        </div>
                        <h2 class="type-h2 local-page__title">
                            {{ item.title }}
                        </h2>
                        <p class="type-body local-page__copy-line">
                            {{ item.summary }}
                        </p>
                    </article>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.local-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.local-page__base {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: minmax(0, 1.15fr) minmax(18rem, 0.85fr);
}

.local-page__panel,
.local-page__rail-card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.local-page__section-head {
    max-width: 46rem;
}

.local-page__copy {
    display: grid;
    gap: var(--sw-space-xs);
}

.local-page__title,
.local-page__copy-line {
    margin: 0;
}

.local-page__title {
    color: var(--sw-text-primary);
}

.local-page__copy-line {
    color: var(--sw-text-secondary);
}

.local-page__signal-list,
.local-page__engagement-list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.local-page__signal-item,
.local-page__engagement-item,
.local-page__note {
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.local-page__signal-item:first-child,
.local-page__engagement-item:first-child {
    border-top: 0;
    padding-top: 0;
}

.local-page__rail {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(18rem, 26rem);
    gap: var(--sw-space-sm);
    overflow-x: auto;
    padding-bottom: var(--sw-space-3xs);
    scroll-snap-type: x proximity;
}

.local-page__rail-link {
    display: block;
    scroll-snap-align: start;
}

.local-page__rail-card {
    height: 100%;
}

.local-page__rail-top,
.local-page__note-top {
    display: grid;
    gap: var(--sw-space-3xs);
    justify-items: start;
}

.local-page__engagements,
.local-page__notes {
    display: grid;
    gap: var(--sw-space-sm);
}

.local-page__engagement {
    display: grid;
    gap: var(--sw-space-xs);
    max-width: 52rem;
    padding-bottom: var(--sw-space-xs);
    border-bottom: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
}

.local-page__note-link {
    color: inherit;
    text-decoration: none;
}

.local-page__note {
    display: grid;
    gap: var(--sw-space-2xs);
    max-width: 52rem;
    transition: border-color var(--sw-motion-fast);
}

@media (hover: hover) {
    .local-page__note-link:hover .local-page__note,
    .local-page__rail-link:hover .local-page__rail-card {
        border-color: var(--sw-accent-dominant);
    }
}

@media (max-width: 960px) {
    .local-page__base {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .local-page {
        gap: var(--sw-space-xs);
    }

    .local-page__rail {
        grid-auto-columns: minmax(16rem, 85vw);
        gap: var(--sw-space-xs);
    }
}
</style>
