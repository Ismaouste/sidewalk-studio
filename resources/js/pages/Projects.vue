<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type {
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
        pills?: string[];
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
          }
        : {
              overviewCta: 'Browse all case studies',
              contactCta: 'Discuss a similar context',
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
          },
);

const uniqueCareerRoles = computed(() =>
    [...new Set(props.careerSnapshot.roles.map((role) => role.trim()))]
        .filter((role) => role !== ''),
);

const hasSideProjectsContent = computed(
    () =>
        props.sideProjectSections.length > 0 ||
        props.sideProjectsWidget.items.length > 0,
);

function sectionTone(label: 'professional' | 'associative' | 'side'): 'dominant' | 'coral' | 'sun' {
    if (label === 'associative') {
        return 'coral';
    }

    if (label === 'side') {
        return 'sun';
    }

    return 'dominant';
}

function detailGridClasses(section: ExperienceSection) {
    return {
        'projects-page__detail-grid--single': section.detail_groups.length === 1,
    };
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
                    <Button href="/case-studies" variant="primary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </SectionIntro>

            <div class="projects-page__work-grid">
                <Panel
                    class="projects-page__work-panel projects-page__work-panel--positioning"
                    tone="surface"
                >
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

                <Panel
                    class="projects-page__work-panel projects-page__work-panel--contexts"
                    tone="grid"
                >
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

                <Panel
                    class="projects-page__work-panel projects-page__work-panel--snapshot"
                    tone="surface"
                >
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
                            v-for="role in uniqueCareerRoles"
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
                        <div class="projects-page__story-copy">
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
                        </div>

                        <div
                            class="projects-page__detail-grid"
                            :class="detailGridClasses(section)"
                        >
                            <div
                                v-for="group in section.detail_groups"
                                :key="group.title"
                                class="projects-page__detail-group"
                            >
                                <p class="type-nav projects-page__detail-title">
                                    {{ group.title }}
                                </p>
                                <div
                                    v-if="group.pills?.length"
                                    class="projects-page__stack-items projects-page__detail-pills"
                                >
                                    <span
                                        v-for="pill in group.pills"
                                        :key="pill"
                                        class="type-meta projects-page__stack-item projects-page__stack-item--detail"
                                    >
                                        {{ pill }}
                                    </span>
                                </div>
                                <ul
                                    class="projects-page__detail-list"
                                >
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
                        <div class="projects-page__story-copy">
                            <p
                                v-for="paragraph in section.paragraphs"
                                :key="paragraph"
                                class="type-body projects-page__copy-line"
                            >
                                {{ paragraph }}
                            </p>
                        </div>

                        <div
                            class="projects-page__detail-grid"
                            :class="detailGridClasses(section)"
                        >
                            <div
                                v-for="group in section.detail_groups"
                                :key="group.title"
                                class="projects-page__detail-group"
                            >
                                <p class="type-nav projects-page__detail-title">
                                    {{ group.title }}
                                </p>
                                <ul
                                    class="projects-page__detail-list"
                                >
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
                    </div>
                </article>
            </div>

            <PublicationWidget :widget="props.associativeNoteWidget" tone="grid" />

            <template v-if="hasSideProjectsContent">
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
                            <div class="projects-page__story-copy">
                                <p
                                    v-for="paragraph in section.paragraphs"
                                    :key="paragraph"
                                    class="type-body projects-page__copy-line"
                                >
                                    {{ paragraph }}
                                </p>
                            </div>

                            <div
                                class="projects-page__detail-grid"
                                :class="detailGridClasses(section)"
                            >
                                <div
                                    v-for="group in section.detail_groups"
                                    :key="group.title"
                                    class="projects-page__detail-group"
                                >
                                    <p class="type-nav projects-page__detail-title">
                                        {{ group.title }}
                                    </p>
                                    <ul
                                        class="projects-page__detail-list"
                                    >
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
                        </div>
                    </article>
                </div>

                <PublicationWidget :widget="props.sideProjectsWidget" tone="surface" />
            </template>

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
    min-width: 0;
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
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: var(--sw-space-sm);
    min-width: 0;
}

.projects-page__work-panel,
.projects-page__case,
.projects-page__closing {
    display: grid;
    align-content: start;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.projects-page__work-panel {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    flex: 1 1 clamp(16rem, 28vw, 24rem);
    max-width: 100%;
    min-width: 0;
    min-width: min(100%, 16rem);
}

.projects-page__closing {
    position: relative;
    gap: var(--sw-space-sm);
    margin-block: clamp(12px, 2vw, 22px) clamp(18px, 3vw, 30px);
    padding: clamp(24px, 3.2vw, 34px);
    padding-block: clamp(34px, 5vw, 52px);
    border-color: color-mix(
        in srgb,
        var(--sw-ambient-flare-soft) 22%,
        var(--sw-border)
    );
    background: color-mix(
        in srgb,
        var(--sw-bg-surface) 88%,
        var(--sw-bg-grid) 12%
    );
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

.projects-page__panel-label {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    width: fit-content;
    margin: 0;
    padding: 0;
    border-radius: 0;
    background: transparent;
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, var(--sw-text-primary));
    font-size: 0.73rem;
    letter-spacing: 0.14em;
}

.projects-page__panel-label::before {
    content: '';
    width: 0.48rem;
    height: 0.48rem;
    border-radius: 9999px;
    background: color-mix(in srgb, var(--sw-accent-dominant) 58%, var(--sw-accent-sun));
}

.projects-page__copy,
.projects-page__stack-groups,
.projects-page__story-links {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.projects-page__story-links {
    justify-items: start;
}

.projects-page__copy-line,
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
    gap: clamp(18px, 2vw, 24px);
    padding: clamp(22px, 2.9vw, 34px);
    min-width: 0;
    border: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-surface);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

.projects-page__story-head,
.projects-page__story-body,
.projects-page__detail-group {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.projects-page__story-head {
    justify-items: start;
    text-align: left;
    gap: clamp(10px, 1.2vw, 16px);
    padding-bottom: clamp(16px, 1.9vw, 22px);
    border-bottom: 1px solid color-mix(in srgb, var(--sw-border) 66%, transparent);
}

.projects-page__story-copy,
.projects-page__detail-group {
    align-content: start;
}

.projects-page__story-body {
    display: grid;
    gap: clamp(20px, 2.1vw, 28px);
    padding-top: clamp(2px, 0.5vw, 6px);
}

.projects-page__detail-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: clamp(18px, 1.9vw, 24px);
    width: 100%;
    min-width: 0;
    padding-top: clamp(18px, 2vw, 24px);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 68%, transparent);
}

.projects-page__detail-grid--single {
    display: flex;
}

.projects-page__detail-pills {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    margin-top: calc(var(--sw-space-3xs) * -0.15);
    align-items: flex-start;
}

.projects-page__story-title {
    margin: 0;
    color: var(--sw-text-primary);
    overflow-wrap: anywhere;
}

.projects-page__story-summary {
    color: color-mix(in srgb, var(--sw-text-primary) 76%, var(--sw-text-secondary));
    max-width: 62rem;
}

.projects-page__story-body .projects-page__copy-line {
    max-width: 62ch;
    line-height: 1.5;
    text-wrap: pretty;
    overflow-wrap: break-word;
}

.projects-page__story-copy {
    gap: clamp(12px, 1.4vw, 18px);
    max-width: 68rem;
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
    gap: clamp(10px, 1.1vw, 14px);
    margin: 0;
    padding: 0;
    list-style: none;
}

.projects-page__detail-group {
    flex: 1 1 clamp(15rem, 22vw, 21rem);
    min-width: min(100%, 15rem);
    max-width: 100%;
}

.projects-page__detail-grid--single .projects-page__detail-group {
    flex-basis: 100%;
}

.projects-page__detail-item,
.projects-page__list-item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 64%, transparent);
    padding-top: clamp(10px, 1.2vw, 14px);
    min-width: 0;
}

.projects-page__detail-item:first-child,
.projects-page__list-item:first-child {
    border-top: 0;
    padding-top: 0;
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
    min-height: 0;
    color: color-mix(in srgb, var(--sw-text-secondary) 74%, var(--sw-text-primary));
    font-size: 0.78rem;
    font-weight: 600;
    line-height: 1.2;
}

.projects-page__stack-item--detail {
    flex: 0 1 auto;
    justify-content: flex-start;
    max-width: 100%;
    padding: 0;
    text-align: left;
    white-space: normal;
    text-wrap: balance;
    overflow-wrap: anywhere;
}

.projects-page__actions :deep(.sw-button) {
    justify-content: flex-start;
}

.projects-page__work-panel--snapshot .projects-page__actions {
    padding-top: var(--sw-space-3xs);
}

.projects-page__work-panel--snapshot {
    gap: var(--sw-space-sm);
    flex: 1.25 1 clamp(18rem, 34vw, 28rem);
    min-width: 0;
}

@supports not (text-wrap: pretty) {
    .projects-page__story-body .projects-page__copy-line {
        word-break: normal;
    }
}

@supports not (text-wrap: balance) {
    .projects-page__stack-item--detail {
        word-break: normal;
    }
}

:global(html[data-theme='sunset']) .projects-page__work-panel--positioning,
:global(html[data-theme='sunset']) .projects-page__work-panel--contexts,
:global(html[data-theme='sunset']) .projects-page__work-panel--snapshot {
    border-color: color-mix(
        in srgb,
        var(--sw-border) 72%,
        var(--sw-accent-violet) 28%
    );
    background: color-mix(in srgb, var(--sw-bg-surface) 88%, transparent);
}

:global(html[data-theme='sunset']) .projects-page__work-panel--positioning {
    background:
        radial-gradient(
            circle at 12% 6%,
            color-mix(in srgb, #6ec5ff 10%, transparent),
            transparent 30%
        ),
        linear-gradient(
            150deg,
            color-mix(in srgb, var(--sw-bg-surface) 92%, #22345e 8%),
            color-mix(in srgb, var(--sw-bg-elevated) 94%, #141b35 6%)
        );
}

:global(html[data-theme='sunset']) .projects-page__work-panel--contexts {
    background:
        radial-gradient(
            circle at 88% 14%,
            color-mix(in srgb, #57b8ff 9%, transparent),
            transparent 24%
        ),
        linear-gradient(
            148deg,
            color-mix(in srgb, var(--sw-bg-surface) 92%, #1b2750 8%),
            color-mix(in srgb, var(--sw-bg-elevated) 94%, #15182f 6%)
        );
}

:global(html[data-theme='sunset']) .projects-page__work-panel--snapshot {
    background:
        radial-gradient(
            circle at 78% 0%,
            color-mix(in srgb, #6ad0ff 9%, transparent),
            transparent 28%
        ),
        linear-gradient(
            144deg,
            color-mix(in srgb, var(--sw-bg-surface) 92%, #22345a 8%),
            color-mix(in srgb, var(--sw-bg-elevated) 94%, #171c39 6%)
        );
}

:global(html[data-theme='sunset']) .projects-page__story,
:global(html[data-theme='sunset']) .projects-page__closing {
    background: color-mix(in srgb, var(--sw-bg-surface) 78%, transparent);
    border-color: color-mix(in srgb, var(--sw-border) 76%, var(--sw-accent-violet) 24%);
}

:global(html[data-theme='sunset']) .projects-page__panel-label {
    background: transparent;
}

@media (min-width: 780px) {
    .projects-page__work-panel--snapshot {
        padding: calc(var(--sw-space-sm) * 1.04);
    }
}

@media (min-width: 1080px) {
    .projects-page__story-copy {
        max-width: 72ch;
    }
}

@media (min-width: 1560px) {
    .projects-page__detail-group {
        flex-basis: clamp(16rem, 18vw, 20rem);
    }
}

@media (max-width: 640px) {
    .projects-page {
        gap: var(--sw-space-xs);
    }

    .projects-page__story {
        padding: var(--sw-space-sm);
    }

    .projects-page__story-body {
        padding-top: var(--sw-space-xs);
    }

    .projects-page__story-head {
        padding-bottom: var(--sw-space-sm);
    }

    .projects-page__panel-label {
        font-size: 0.7rem;
        padding: 0.42rem 0.68rem;
    }

    .projects-page__closing {
        margin-block: var(--sw-space-sm) var(--sw-space-md);
        padding: var(--sw-space-md);
        padding-block: clamp(28px, 9vw, 38px);
    }

    .projects-page__detail-pills {
        gap: 0.45rem;
    }
}

</style>
