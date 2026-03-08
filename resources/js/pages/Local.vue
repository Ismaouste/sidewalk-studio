<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { PublicationWidget as PublicationWidgetData, SeoPayload, SiteProps } from '@/types';

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
        signals: string[];
    };
    citySystems: Array<{
        title: string;
        summary: string;
    }>;
    communities: Array<{
        title: string;
        summary: string;
    }>;
    supportAreas: Array<{
        title: string;
        summary: string;
        items: string[];
    }>;
    journalWidget: PublicationWidgetData;
}>();

const localTones = ['dominant', 'green', 'coral'] as const;

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Voir les références',
              contactCta: 'Prendre contact',
              heroChipBase: 'Sillon lorrain',
              heroChipCivic: 'Systèmes civiques',
              heroChipHealth: 'Santé publique',
              baseDivider: 'Terrain',
              signalsLabel: "Signaux que je regarde",
              signalPrefix: 'Signal',
              cityDivider: 'Villes et systèmes',
              communityDivider: 'Associations et engagements',
              supportDivider: 'Sujets que je soutiens',
              supportPoint: 'Point',
              closingDivider: 'Retour au travail',
              closingCopy:
                  "Cette page sert à rendre visible le terrain depuis lequel je travaille. Les mêmes sujets réapparaissent ailleurs sous forme de références, de journal et de contextes de collaboration.",
              writingCta: 'Ouvrir le journal',
          }
        : {
              projectsCta: 'Browse references',
              contactCta: 'Contact',
              heroChipBase: 'Lorraine corridor',
              heroChipCivic: 'Civic systems',
              heroChipHealth: 'Public health',
              baseDivider: 'Ground',
              signalsLabel: 'Signals I pay attention to',
              signalPrefix: 'Signal',
              cityDivider: 'Cities and systems',
              communityDivider: 'Communities and commitments',
              supportDivider: 'Projects and civic questions',
              supportPoint: 'Point',
              closingDivider: 'Back into the work',
              closingCopy:
                  'This page makes the ground visible. The same concerns return elsewhere as work references, journal entries, and collaboration contexts.',
              writingCta: 'Open the journal',
          },
);
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
                <LegendChip :label="copy.heroChipCivic" tone="green" />
                <LegendChip :label="copy.heroChipHealth" tone="sun" />
            </SectionIntro>

            <SectionDivider :label="copy.baseDivider" />

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

                    <ul class="local-page__list">
                        <li
                            v-for="signal in props.nancy.signals"
                            :key="signal"
                            class="local-page__list-item"
                        >
                            <LegendChip
                                :label="copy.signalPrefix"
                                tone="green"
                            />
                            <p class="type-body local-page__copy-line">
                                {{ signal }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>

            <SectionDivider :label="copy.cityDivider" />

            <div class="local-page__grid">
                <Panel
                    v-for="(item, index) in props.citySystems"
                    :key="item.title"
                    class="local-page__card"
                    tone="surface"
                >
                    <LegendChip
                        :label="`${copy.signalPrefix} 0${index + 1}`"
                        :tone="localTones[index] ?? 'violet'"
                    />
                    <h2 class="type-h2 local-page__title">
                        {{ item.title }}
                    </h2>
                    <p class="type-body local-page__copy-line">
                        {{ item.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider :label="copy.communityDivider" />

            <div class="local-page__grid">
                <Panel
                    v-for="community in props.communities"
                    :key="community.title"
                    class="local-page__card"
                    tone="grid"
                >
                    <h2 class="type-h2 local-page__title">
                        {{ community.title }}
                    </h2>
                    <p class="type-body local-page__copy-line">
                        {{ community.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider :label="copy.supportDivider" />

            <div class="local-page__grid">
                <Panel
                    v-for="area in props.supportAreas"
                    :key="area.title"
                    class="local-page__card"
                    tone="surface"
                >
                    <h2 class="type-h2 local-page__title">
                        {{ area.title }}
                    </h2>
                    <p class="type-body local-page__copy-line">
                        {{ area.summary }}
                    </p>

                    <ul class="local-page__list">
                        <li
                            v-for="item in area.items"
                            :key="item"
                            class="local-page__list-item"
                        >
                            <LegendChip
                                :label="copy.supportPoint"
                                tone="coral"
                            />
                            <p class="type-body local-page__copy-line">
                                {{ item }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>

            <SectionDivider :label="copy.closingDivider" />

            <Panel class="local-page__closing" tone="grid">
                <p class="type-body local-page__copy-line">
                    {{ copy.closingCopy }}
                </p>

                <div class="local-page__actions">
                    <Button href="/projects">{{ copy.projectsCta }}</Button>
                    <Button href="/writing" variant="secondary">
                        {{ copy.writingCta }}
                    </Button>
                    <Button href="/contact" variant="ghost">{{
                        copy.contactCta
                    }}</Button>
                </div>
            </Panel>

            <PublicationWidget :widget="props.journalWidget" tone="surface" />
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
    grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
}

.local-page__panel,
.local-page__card,
.local-page__closing {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.local-page__copy {
    display: grid;
    gap: var(--sw-space-xs);
}

.local-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
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

.local-page__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.local-page__list-item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.local-page__list-item:first-child {
    border-top: 0;
    padding-top: 0;
}

.local-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

@media (max-width: 960px) {
    .local-page__base,
    .local-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
