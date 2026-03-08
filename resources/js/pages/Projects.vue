<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type {
    ContentItem,
    PublicationWidget as PublicationWidgetData,
    SeoPayload,
    SiteProps,
} from '@/types';

type ExperienceSection = {
    title: string;
    eyebrow: string;
    summary: string;
    paragraphs: string[];
    detail_groups: Array<{
        title: string;
        items: string[];
    }>;
};

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    positioning: string[];
    contexts: string[];
    stackGroups: Array<{
        title: string;
        items: string[];
    }>;
    careerSnapshot: {
        title: string;
        summary: string;
        roles: string[];
    };
    lookingFor: string;
    professionalSections: ExperienceSection[];
    associativeSections: ExperienceSection[];
    sideProjectSections: ExperienceSection[];
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
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
    associativeNoteWidget: PublicationWidgetData;
    sideProjectsWidget: PublicationWidgetData;
    journalWidget: PublicationWidgetData;
    referenceWidget: PublicationWidgetData;
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              overviewCta: "Voir les cas d'étude",
              contactCta: "Discuter d'un contexte proche",
              internalBuildLabel: 'Interne',
              roleLabel: 'Rôle',
              contextTagLabel: 'Contexte',
              workFrameLabel: 'Cadre',
              positioningLabel: 'Comment je travaille',
              contextsLabel: 'Contextes',
              recruiterLabel: 'Repère rapide',
              experienceLabel: 'Expériences pro',
              associativeLabel: 'Aremedia',
              sideProjectsLabel: 'Projets amis / fun',
              stackLabel: 'Stack',
              lookingForLabel: 'Ce que je recherche',
              outcomesLabel: 'Résultats',
              outcomesSuffix: 'points',
          }
        : {
              overviewCta: 'Open case studies',
              contactCta: 'Discuss a similar context',
              internalBuildLabel: 'Internal build',
              roleLabel: 'Role',
              contextTagLabel: 'Context',
              workFrameLabel: 'Frame',
              positioningLabel: 'How I work',
              contextsLabel: 'Contexts',
              recruiterLabel: 'Quick snapshot',
              experienceLabel: 'Professional experience',
              associativeLabel: 'Aremedia',
              sideProjectsLabel: 'Friends / fun projects',
              stackLabel: 'Stack',
              lookingForLabel: 'What I am looking for',
              outcomesLabel: 'Outcomes',
              outcomesSuffix: 'signals',
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

function sectionTone(label: 'professional' | 'associative' | 'side'): 'dominant' | 'coral' | 'sun' {
    if (label === 'associative') {
        return 'coral';
    }

    if (label === 'side') {
        return 'sun';
    }

    return 'dominant';
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
                    <Button href="/case-studies" variant="secondary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost">
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </SectionIntro>

            <SectionDivider :label="copy.workFrameLabel" />

            <div class="projects-page__work-grid">
                <Panel class="projects-page__work-panel" tone="surface">
                    <p class="type-eyebrow">{{ copy.positioningLabel }}</p>
                    <div class="projects-page__copy">
                        <p
                            v-for="paragraph in props.positioning"
                            :key="paragraph"
                            class="type-body projects-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>
                    </div>
                </Panel>

                <Panel class="projects-page__work-panel" tone="grid">
                    <p class="type-eyebrow">{{ copy.contextsLabel }}</p>
                    <ul class="projects-page__list">
                        <li
                            v-for="context in props.contexts"
                            :key="context"
                            class="projects-page__list-item"
                        >
                            <LegendChip
                                :label="copy.contextTagLabel"
                                tone="green"
                            />
                            <p class="type-body projects-page__copy-line">
                                {{ context }}
                            </p>
                        </li>
                    </ul>
                </Panel>

                <Panel class="projects-page__work-panel" tone="surface">
                    <p class="type-eyebrow">{{ copy.recruiterLabel }}</p>
                    <p
                        v-if="props.careerSnapshot.summary"
                        class="type-body projects-page__copy-line"
                    >
                        {{ props.careerSnapshot.summary }}
                    </p>

                    <div class="projects-page__stack-items">
                        <span
                            v-for="role in props.careerSnapshot.roles"
                            :key="role"
                            class="type-meta projects-page__stack-item"
                        >
                            {{ role }}
                        </span>
                    </div>

                    <div class="projects-page__actions">
                        <Button
                            v-for="download in props.cvDownloads"
                            :key="download.href"
                            :href="download.href"
                            variant="ghost"
                        >
                            {{ download.label }}
                        </Button>
                    </div>
                </Panel>
            </div>

            <SectionDivider :label="copy.experienceLabel" />

            <div class="projects-page__section-list">
                <article
                    v-for="section in props.professionalSections"
                    :key="section.title"
                    class="projects-page__story"
                >
                    <div class="projects-page__story-head">
                        <LegendChip
                            :label="section.eyebrow"
                            :tone="sectionTone('professional')"
                        />
                        <h2 class="type-h2 projects-page__story-title">
                            {{ section.title }}
                        </h2>
                        <p class="type-body projects-page__story-summary">
                            {{ section.summary }}
                        </p>
                    </div>

                    <div class="projects-page__story-body">
                        <p
                            v-for="paragraph in section.paragraphs"
                            :key="paragraph"
                            class="type-body projects-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>

                        <div
                            v-for="group in section.detail_groups"
                            :key="group.title"
                            class="projects-page__detail-group"
                        >
                            <p class="type-nav projects-page__detail-title">
                                {{ group.title }}
                            </p>
                            <ul class="projects-page__detail-list">
                                <li
                                    v-for="item in group.items"
                                    :key="item"
                                    class="projects-page__detail-item"
                                >
                                    <p class="type-body projects-page__copy-line">
                                        {{ item }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>
            </div>

            <SectionDivider :label="copy.associativeLabel" />

            <div class="projects-page__section-list">
                <article
                    v-for="section in props.associativeSections"
                    :key="section.title"
                    class="projects-page__story"
                >
                    <div class="projects-page__story-head">
                        <LegendChip
                            :label="section.eyebrow"
                            :tone="sectionTone('associative')"
                        />
                        <h2 class="type-h2 projects-page__story-title">
                            {{ section.title }}
                        </h2>
                        <p class="type-body projects-page__story-summary">
                            {{ section.summary }}
                        </p>
                    </div>

                    <div class="projects-page__story-body">
                        <p
                            v-for="paragraph in section.paragraphs"
                            :key="paragraph"
                            class="type-body projects-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>

                        <div
                            v-for="group in section.detail_groups"
                            :key="group.title"
                            class="projects-page__detail-group"
                        >
                            <p class="type-nav projects-page__detail-title">
                                {{ group.title }}
                            </p>
                            <ul class="projects-page__detail-list">
                                <li
                                    v-for="item in group.items"
                                    :key="item"
                                    class="projects-page__detail-item"
                                >
                                    <p class="type-body projects-page__copy-line">
                                        {{ item }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>
            </div>

            <PublicationWidget :widget="props.associativeNoteWidget" tone="grid" />

            <SectionDivider :label="copy.sideProjectsLabel" />

            <div class="projects-page__section-list">
                <article
                    v-for="section in props.sideProjectSections"
                    :key="section.title"
                    class="projects-page__story"
                >
                    <div class="projects-page__story-head">
                        <LegendChip
                            :label="section.eyebrow"
                            :tone="sectionTone('side')"
                        />
                        <h2 class="type-h2 projects-page__story-title">
                            {{ section.title }}
                        </h2>
                        <p class="type-body projects-page__story-summary">
                            {{ section.summary }}
                        </p>
                    </div>

                    <div class="projects-page__story-body">
                        <p
                            v-for="paragraph in section.paragraphs"
                            :key="paragraph"
                            class="type-body projects-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>

                        <div
                            v-for="group in section.detail_groups"
                            :key="group.title"
                            class="projects-page__detail-group"
                        >
                            <p class="type-nav projects-page__detail-title">
                                {{ group.title }}
                            </p>
                            <ul class="projects-page__detail-list">
                                <li
                                    v-for="item in group.items"
                                    :key="item"
                                    class="projects-page__detail-item"
                                >
                                    <p class="type-body projects-page__copy-line">
                                        {{ item }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>
            </div>

            <PublicationWidget :widget="props.sideProjectsWidget" tone="surface" />

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
                        <ContentVisual :item="item" compact />
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

            <Panel class="projects-page__closing" tone="grid">
                <p class="type-eyebrow">{{ copy.lookingForLabel }}</p>
                <p class="type-body projects-page__copy-line">
                    {{ props.lookingFor }}
                </p>

                <div class="projects-page__stack-groups">
                    <section
                        v-for="group in props.stackGroups"
                        :key="group.title"
                        class="projects-page__stack-group"
                    >
                        <p class="type-nav">{{ group.title }}</p>
                        <div class="projects-page__stack-items">
                            <span
                                v-for="item in group.items"
                                :key="item"
                                class="type-meta projects-page__stack-item"
                            >
                                {{ item }}
                            </span>
                        </div>
                    </section>
                </div>
            </Panel>

            <PublicationWidget :widget="props.journalWidget" tone="surface" />
            <PublicationWidget :widget="props.referenceWidget" tone="grid" />
        </section>
    </SiteLayout>
</template>

<style scoped>
.projects-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.projects-page__work-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.projects-page__work-panel,
.projects-page__case,
.projects-page__closing {
    display: grid;
    align-content: start;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.projects-page__copy,
.projects-page__stack-groups {
    display: grid;
    gap: var(--sw-space-xs);
}

.projects-page__copy-line,
.projects-page__case-summary,
.projects-page__case-role,
.projects-page__story-summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.projects-page__section-list {
    display: grid;
    gap: var(--sw-space-md);
}

.projects-page__story {
    display: grid;
    gap: var(--sw-space-sm);
    max-width: 64rem;
    padding-bottom: var(--sw-space-sm);
    border-bottom: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
}

.projects-page__story-head,
.projects-page__story-body,
.projects-page__detail-group {
    display: grid;
    gap: var(--sw-space-xs);
}

.projects-page__story-title,
.projects-page__case-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.projects-page__detail-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.projects-page__detail-list,
.projects-page__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.projects-page__detail-item,
.projects-page__list-item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.projects-page__detail-item:first-child,
.projects-page__list-item:first-child {
    border-top: 0;
    padding-top: 0;
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

.projects-page__case-top {
    display: grid;
    gap: var(--sw-space-3xs);
    align-items: start;
    justify-items: start;
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

.projects-page__case-tags,
.projects-page__stack-items,
.projects-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.projects-page__stack-item,
.projects-page__case-tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 84%, transparent);
    padding-inline: var(--sw-space-2xs);
    color: var(--sw-text-primary);
    box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--sw-border) 72%, transparent);
}

.projects-page__case-link:focus-visible {
    outline: none;
}

.projects-page__case-link:focus-visible .projects-page__case,
.projects-page__case-link:active .projects-page__case {
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
}

@media (max-width: 960px) {
    .projects-page__work-grid,
    .projects-page__cases {
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
