<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
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
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    tracksSection: {
        label: string;
        intro: {
            eyebrow: string;
            title: string;
            summary: string;
        };
        items: { title: string; summary: string }[];
    };
    caseStudies: ContentItem[];
    caseStudiesSection: {
        label: string;
        eyebrow: string;
        title: string;
        summary: string;
        archive_cta: string;
    };
    latestWriting: ContentItem[];
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              experienceCta: 'Lire le parcours',
              contactCta: "Discuter d'un contexte proche",
              trackPrefix: 'Piste',
              internalBuildLabel: 'Interne',
              roleLabel: 'Role',
              outcomesLabel: 'Points saillants',
              outcomesSuffix: 'points',
              proofNotesLabel: 'Notes',
              proofNotesTitle:
                  'Les notes qui explicitent le raisonnement.',
              proofNotesSummary:
                  "Quand un cas client reste volontairement compact, les notes publiques explicitent le raisonnement d'architecture, de contenu, de modele catalogue et de SEO qui l'accompagne.",
              writingArchiveCta: 'Voir les notes',
              writingNoteLabel: 'Note',
          }
        : {
              experienceCta: 'Read experience',
              contactCta: 'Discuss a similar context',
              trackPrefix: 'Track',
              internalBuildLabel: 'Internal build',
              roleLabel: 'Role',
              outcomesLabel: 'Outcomes',
              outcomesSuffix: 'signals',
              proofNotesLabel: 'Associated notes',
              proofNotesTitle:
                  'Notes that detail the reasoning behind the build.',
              proofNotesSummary:
                  'When a case study stays intentionally compact, public notes detail the architecture, content, catalog-modeling, and SEO reasoning that sits behind it.',
              writingArchiveCta: 'Open writing',
              writingNoteLabel: 'Writing',
          },
);

function caseStudyMeta(item: ContentItem) {
    return [
        {
            label: copy.value.roleLabel,
            value: item.role,
        },
        {
            label: copy.value.outcomesLabel,
            value: `${item.outcomes.length} ${copy.value.outcomesSuffix}`,
        },
    ];
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section projects-page">
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <template #actions>
                    <Button href="/experience" variant="secondary">
                        {{ copy.experienceCta }}
                    </Button>
                    <Button href="/contact" variant="ghost">
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </SectionIntro>

            <div class="projects-page__tracks">
                <Panel
                    v-for="(track, index) in props.tracksSection.items"
                    :key="track.title"
                    class="projects-page__track"
                    tone="surface"
                >
                    <LegendChip
                        :label="`${copy.trackPrefix} 0${index + 1}`"
                        :tone="
                            index === 0
                                ? 'dominant'
                                : index === 1
                                  ? 'green'
                                  : 'coral'
                        "
                    />
                    <h3 class="type-h2 projects-page__track-title">
                        {{ track.title }}
                    </h3>
                    <p class="type-body projects-page__track-summary">
                        {{ track.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider :label="props.caseStudiesSection.label" />

            <div class="projects-page__header">
                <SectionIntro
                    :eyebrow="props.caseStudiesSection.eyebrow"
                    :title="props.caseStudiesSection.title"
                    :description="props.caseStudiesSection.summary"
                />
                <Button href="/case-studies" variant="ghost">
                    {{ props.caseStudiesSection.archive_cta }}
                </Button>
            </div>

            <div class="projects-page__cases">
                <Link
                    v-for="item in props.caseStudies"
                    :key="item.slug"
                    :href="item.url"
                    class="projects-page__case-link"
                >
                    <Panel class="projects-page__case" tone="grid">
                        <div class="projects-page__case-top">
                            <LegendChip
                                :label="item.client || copy.internalBuildLabel"
                                tone="green"
                            />
                            <ContentMetaRow :items="caseStudyMeta(item)" />
                        </div>
                        <h3 class="type-h2 projects-page__case-title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body-sm projects-page__case-role">
                            {{ item.role }}
                        </p>
                        <p class="type-body projects-page__case-summary">
                            {{ item.summary }}
                        </p>
                        <div class="projects-page__case-tags">
                            <span
                                v-for="tag in item.tags"
                                :key="tag"
                                class="type-meta projects-page__case-tag"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </Panel>
                </Link>
            </div>

            <SectionDivider :label="copy.proofNotesLabel" />

            <div class="projects-page__header">
                <SectionIntro
                    :eyebrow="copy.proofNotesLabel"
                    :title="copy.proofNotesTitle"
                    :description="copy.proofNotesSummary"
                />
                <Button href="/writing" variant="ghost">
                    {{ copy.writingArchiveCta }}
                </Button>
            </div>

            <div class="projects-page__notes">
                <Link
                    v-for="item in props.latestWriting"
                    :key="item.slug"
                    :href="item.url"
                    class="projects-page__note-link"
                >
                    <Panel class="projects-page__note" tone="surface">
                        <LegendChip
                            v-if="page.props.site.locale === 'fr'"
                            :label="copy.writingNoteLabel"
                            tone="violet"
                        />
                        <LegendChip
                            v-else
                            label="Writing"
                            tone="violet"
                        />
                        <h3 class="type-h2 projects-page__case-title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body projects-page__case-summary">
                            {{ item.summary }}
                        </p>
                    </Panel>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.projects-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.projects-page__tracks {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.projects-page__track,
.projects-page__case {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.projects-page__track-title,
.projects-page__case-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.projects-page__track-summary,
.projects-page__case-summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.projects-page__case-role {
    margin: 0;
    color: var(--sw-text-secondary);
}

.projects-page__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
    align-items: end;
    justify-content: space-between;
}

.projects-page__header :deep(.section-intro) {
    max-width: 46rem;
}

.projects-page__cases {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.projects-page__case-link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.projects-page__notes {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.projects-page__note-link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.projects-page__case {
    position: relative;
    height: 100%;
    overflow: hidden;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.projects-page__note {
    display: grid;
    gap: var(--sw-space-xs);
    height: 100%;
    padding: var(--sw-space-sm);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.projects-page__case-top {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    align-items: center;
    justify-content: space-between;
}

.projects-page__case::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 3px;
    transform: scaleX(0.24);
    transform-origin: left center;
    opacity: 0;
    background: linear-gradient(
        90deg,
        var(--sw-accent-dominant),
        color-mix(in srgb, var(--sw-accent-sun) 68%, var(--sw-accent-dominant))
    );
    transition:
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
}

.projects-page__case-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.projects-page__case-tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.projects-page__case-link:focus-visible {
    outline: none;
}

.projects-page__note-link:focus-visible {
    outline: none;
}

.projects-page__case-link:focus-visible .projects-page__case,
.projects-page__case-link:active .projects-page__case {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.projects-page__note-link:focus-visible .projects-page__note,
.projects-page__note-link:active .projects-page__note {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.projects-page__case-link:focus-visible .projects-page__case::before,
.projects-page__case-link:active .projects-page__case::before {
    transform: scaleX(1);
    opacity: 1;
}

.projects-page__case-link:active .projects-page__case {
    transform: translateY(1px);
}

.projects-page__note-link:active .projects-page__note {
    transform: translateY(1px);
}

@media (hover: hover) {
    .projects-page__case-link:hover .projects-page__case {
        transform: translateY(-2px);
        border-color: var(--sw-accent-dominant);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
        box-shadow: var(--sw-shadow-md);
    }

    .projects-page__case-link:hover .projects-page__case::before {
        transform: scaleX(1);
        opacity: 1;
    }

    .projects-page__note-link:hover .projects-page__note {
        transform: translateY(-2px);
        border-color: var(--sw-accent-violet);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
        box-shadow: var(--sw-shadow-md);
    }
}

@media (max-width: 960px) {
    .projects-page__tracks,
    .projects-page__cases,
    .projects-page__notes {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .projects-page {
        gap: var(--sw-space-xs);
    }

    .projects-page__header {
        gap: var(--sw-space-xs);
        align-items: start;
    }
}
</style>
