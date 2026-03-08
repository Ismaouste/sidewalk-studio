<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
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
    links?: Array<{
        label: string;
        href: string;
    }>;
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
              overviewCta: "Découvrir toutes les études de cas",
              contactCta: "Discuter d'un contexte proche",
              internalBuildLabel: 'Interne',
              roleLabel: 'Rôle',
              contextTitles: [
                  'Commerce en charge',
                  'Stacks reprises',
                  'Refontes cadrées',
              ],
              workFrameLabel: 'Cadre',
              positioningLabel: 'Comment je travaille',
              contextsLabel: 'Contextes',
              recruiterLabel: 'Repères techniques',
              experienceLabel: 'Expériences pro',
              associativeLabel: 'Santé publique et associatif',
              sideProjectsLabel: 'Culture et hobby',
              stackLabel: 'Stack',
              lookingForLabel: 'Ce que je recherche',
              outcomesLabel: 'Résultats',
              outcomesSuffix: 'points',
          }
        : {
              overviewCta: 'Browse all case studies',
              contactCta: 'Discuss a similar context',
              internalBuildLabel: 'Internal build',
              roleLabel: 'Role',
              contextTitles: [
                  'Live commerce',
                  'Recovered stacks',
                  'Redesigns',
              ],
              workFrameLabel: 'Frame',
              positioningLabel: 'How I work',
              contextsLabel: 'Contexts',
              recruiterLabel: 'Technical bearings',
              experienceLabel: 'Professional experience',
              associativeLabel: 'Public health and nonprofit',
              sideProjectsLabel: 'Culture and hobbies',
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
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </SectionIntro>

            <div class="projects-page__work-grid">
                <Panel class="projects-page__work-panel" tone="surface">
                    <p class="type-eyebrow projects-page__panel-label">
                        {{ copy.positioningLabel }}
                    </p>
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
                    <p class="type-eyebrow projects-page__panel-label">
                        {{ copy.contextsLabel }}
                    </p>
                    <ul class="projects-page__list">
                        <li
                            v-for="(context, index) in props.contexts"
                            :key="context"
                            class="projects-page__list-item"
                        >
                            <LegendChip
                                :label="
                                    copy.contextTitles[index] ??
                                    props.contexts[index]
                                "
                                tone="green"
                            />
                            <p class="type-body projects-page__copy-line">
                                {{ context }}
                            </p>
                        </li>
                    </ul>
                </Panel>

                <Panel class="projects-page__work-panel" tone="surface">
                    <p class="type-eyebrow projects-page__panel-label">
                        {{ copy.recruiterLabel }}
                    </p>
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

            <p class="type-eyebrow projects-page__section-label">
                {{ copy.experienceLabel }}
            </p>

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
                            v-if="section.links?.length"
                            class="projects-page__story-links"
                        >
                            <Button
                                v-for="link in section.links"
                                :key="link.href"
                                :href="link.href"
                                external
                                variant="ghost"
                                arrow
                            >
                                {{ link.label }}
                            </Button>
                        </div>

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

            <p
                class="type-eyebrow projects-page__section-label projects-page__section-label--associative"
            >
                {{ copy.associativeLabel }}
            </p>

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

            <p class="type-eyebrow projects-page__section-label">
                {{ copy.sideProjectsLabel }}
            </p>

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

            <div class="projects-page__header">
                <SectionIntro
                    :eyebrow="props.caseStudiesSection.eyebrow"
                    :title="props.caseStudiesSection.title"
                    :description="props.caseStudiesSection.summary"
                />
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

            <div class="projects-page__footer-cta">
                <Button href="/case-studies" variant="ghost" arrow>
                    {{ props.caseStudiesSection.archive_cta }}
                </Button>
            </div>

            <Panel class="projects-page__closing" tone="grid">
                <p class="type-eyebrow projects-page__panel-label">
                    {{ copy.lookingForLabel }}
                </p>
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

.projects-page__section-label {
    width: fit-content;
    margin: 0;
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, var(--sw-text-primary));
    font-size: 0.72rem;
    letter-spacing: 0.16em;
}

.projects-page__section-label--associative {
    padding-top: clamp(10px, 1.8vw, 18px);
}

.projects-page__work-grid {
    display: grid;
    align-items: start;
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

.projects-page__closing {
    position: relative;
    gap: var(--sw-space-sm);
    padding-block: clamp(28px, 4vw, 42px);
    border-color: color-mix(
        in srgb,
        var(--sw-ambient-flare-soft) 22%,
        var(--sw-border)
    );
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, white 8%, transparent),
            transparent 32%
        ),
        radial-gradient(
            circle at 14% 0%,
            color-mix(in srgb, var(--sw-ambient-flare-soft) 22%, transparent),
            transparent 42%
        ),
        radial-gradient(
            circle at 86% 100%,
            color-mix(in srgb, var(--sw-ambient-flare) 10%, transparent),
            transparent 48%
        ),
        color-mix(
            in srgb,
            var(--sw-bg-surface) 74%,
            var(--sw-ambient-flare-soft) 26%
        );
    box-shadow: var(--sw-shadow-md);
    -webkit-backdrop-filter: blur(24px) saturate(120%);
    backdrop-filter: blur(24px) saturate(120%);
}

.projects-page__panel-label {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    width: fit-content;
    margin: 0;
    padding: 0.48rem 0.95rem 0.48rem 0.8rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 74%, transparent);
    box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--sw-border) 66%, transparent);
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, var(--sw-text-primary));
    font-size: 0.73rem;
    letter-spacing: 0.14em;
}

.projects-page__panel-label::before {
    content: '';
    width: 0.48rem;
    height: 0.48rem;
    border-radius: 999px;
    background: color-mix(in srgb, var(--sw-accent-dominant) 58%, var(--sw-accent-sun));
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--sw-accent-dominant) 10%, transparent);
}

.projects-page__copy,
.projects-page__stack-groups,
.projects-page__story-links {
    display: grid;
    gap: var(--sw-space-xs);
}

.projects-page__story-links {
    justify-items: start;
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

.projects-page__story-summary {
    color: color-mix(in srgb, var(--sw-text-primary) 76%, var(--sw-text-secondary));
}

.projects-page__story-body {
    max-width: 62rem;
}

.projects-page__story-body .projects-page__copy-line {
    line-height: 1.56;
}

.projects-page__detail-title {
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.65rem 0.5rem 0.55rem 0;
    color: color-mix(in srgb, var(--sw-text-secondary) 88%, var(--sw-text-primary));
    font-size: 0.84rem;
    letter-spacing: 0.13em;
}

.projects-page__detail-title::before {
    content: '•';
    color: color-mix(in srgb, var(--sw-accent-dominant) 74%, var(--sw-accent-sun));
    font-size: 0.92em;
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
    display: grid;
    gap: var(--sw-space-xs);
}

.projects-page__header :deep(.section-intro) {
    max-width: 46rem;
}

.projects-page__footer-cta {
    display: flex;
    justify-content: flex-start;
}

.projects-page__footer-cta :deep(.sw-button) {
    max-width: 16rem;
    white-space: normal;
    line-height: 1.3;
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
    height: 100%;
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
    min-height: 1.9rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    padding-inline: 0.8rem;
    color: color-mix(in srgb, var(--sw-text-secondary) 74%, var(--sw-text-primary));
    font-size: 0.78rem;
    font-weight: 600;
    box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--sw-border) 68%, transparent);
}

.projects-page__actions :deep(.sw-button) {
    justify-content: flex-start;
}

.projects-page__case-link:focus-visible {
    outline: none;
}

.projects-page__case-link:focus-visible .projects-page__case,
.projects-page__case-link:active .projects-page__case {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.projects-page__case-link:active .projects-page__case {
    transform: translateY(1px);
}

@media (hover: hover) {
    .projects-page__case-link:hover .projects-page__case {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
        box-shadow: var(--sw-shadow-md);
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

    .projects-page__panel-label {
        font-size: 0.7rem;
        padding: 0.42rem 0.68rem;
    }

    .projects-page__header {
        gap: var(--sw-space-xs);
        align-items: start;
    }
}
</style>
