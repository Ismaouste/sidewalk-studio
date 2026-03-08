<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
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
    positioning: string[];
    contexts: string[];
    trajectory: Array<{
        title: string;
        summary: string;
    }>;
    strengths: string[];
    focusAreas: Array<{
        title: string;
        summary: string;
    }>;
    stackGroups: Array<{
        title: string;
        items: string[];
    }>;
    careerSnapshot: {
        title: string;
        summary: string;
        roles: string[];
    };
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
    featuredCaseStudies: ContentItem[];
    latestWriting: ContentItem[];
    lookingFor: string;
}>();

const trajectoryTones = ['dominant', 'green', 'coral'] as const;

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Voir les references',
              contactCta: 'Prendre contact',
              currentPositioningLabel: 'Positionnement',
              positioningLabel: 'Comment je travaille maintenant',
              contextsLabel: 'Contextes typiques',
              trajectoryLabel: 'Parcours professionnel',
              phasePrefix: 'Phase',
              technicalFocusLabel: 'Focus techniques',
              strengthsLabel: 'Forces et environnements',
              strengthsWorkingStyleLabel: 'Forces et style de travail',
              stackEnvironmentsLabel: 'Stack et environnements',
              publicProofLabel: 'References',
              publicProofSummary:
                  "Cas clients et notes publiques montrent comment le travail se traduit en decisions d'architecture, de SEO technique, de structure catalogue et de livraison.",
              caseStudiesLabel: 'Cas clients',
              writingLabel: 'Notes',
              openArchiveCta: 'Voir les notes',
              quickHandoffLabel: 'Lecture rapide',
              quickHandoffSummary:
                  "Si quelqu'un doit cerner le profil rapidement, les CV, les roles cibles et la page contact donnent deja assez de matiere pour avancer sans premier echange flou.",
              recruiterSnapshotLabel: 'Repere recruteur',
              whatLookingForLabel: 'Ce que je recherche',
              writingCta: 'Notes',
              caseStudiesCta: 'Cas clients',
              heroChipCommerceLabel: 'E-commerce',
              heroChipFrameworkLabel: 'Laravel',
              heroChipSeoLabel: 'SEO technique',
              contextChipLabel: 'Contexte',
              strengthChipLabel: 'Point fort',
          }
        : {
              projectsCta: 'Browse references',
              contactCta: 'Start a conversation',
              currentPositioningLabel: 'Current positioning',
              positioningLabel: 'How I work now',
              contextsLabel: 'Typical contexts',
              trajectoryLabel: 'Professional trajectory',
              phasePrefix: 'Phase',
              technicalFocusLabel: 'Selected technical focus',
              strengthsLabel: 'Strengths and environments',
              strengthsWorkingStyleLabel: 'Strengths and working style',
              stackEnvironmentsLabel: 'Stack and environments',
              publicProofLabel: 'References',
              publicProofSummary:
                  'Public case studies and notes show how the work translates into architecture decisions, technical SEO, catalog structure, and delivery discipline.',
              caseStudiesLabel: 'Case studies',
              writingLabel: 'Writing',
              openArchiveCta: 'Open archive',
              quickHandoffLabel: 'Quick handoff',
              quickHandoffSummary:
                  'If you need to qualify the profile quickly, the CV package, role targets, and contact page already give enough material to move without a vague first call.',
              recruiterSnapshotLabel: 'Recruiter-ready snapshot',
              whatLookingForLabel: 'What I am looking for',
              writingCta: 'Writing',
              caseStudiesCta: 'Case studies',
              heroChipCommerceLabel: 'E-commerce',
              heroChipFrameworkLabel: 'Laravel',
              heroChipSeoLabel: 'Technical SEO',
              contextChipLabel: 'Context',
              strengthChipLabel: 'Strength',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section experience-page">
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

                <LegendChip
                    :label="
                        page.props.site.locale === 'fr'
                            ? copy.heroChipCommerceLabel
                            : 'E-commerce'
                    "
                    tone="dominant"
                />
                <LegendChip
                    :label="
                        page.props.site.locale === 'fr'
                            ? copy.heroChipFrameworkLabel
                            : 'Laravel'
                    "
                    tone="green"
                />
                <LegendChip
                    :label="
                        page.props.site.locale === 'fr'
                            ? copy.heroChipSeoLabel
                            : 'Technical SEO'
                    "
                    tone="sun"
                />
            </SectionIntro>

            <div class="experience-page__proof-grid">
                <Panel class="experience-page__proof-card" tone="grid">
                    <p class="type-eyebrow">
                        {{ copy.recruiterSnapshotLabel }}
                    </p>
                    <p class="type-body experience-page__copy-line">
                        {{ props.careerSnapshot.summary }}
                    </p>

                    <div class="experience-page__stack-items">
                        <span
                            v-for="role in props.careerSnapshot.roles"
                            :key="role"
                            class="type-meta experience-page__stack-item"
                        >
                            {{ role }}
                        </span>
                    </div>
                </Panel>

                <Panel class="experience-page__proof-card" tone="surface">
                    <p class="type-eyebrow">{{ copy.publicProofLabel }}</p>
                    <p class="type-body experience-page__copy-line">
                        {{ copy.publicProofSummary }}
                    </p>

                    <div class="experience-page__proof-columns">
                        <div class="experience-page__proof-column">
                            <p class="type-nav">
                                {{ copy.caseStudiesLabel }}
                            </p>
                            <Link
                                v-for="item in props.featuredCaseStudies"
                                :key="item.slug"
                                :href="item.url"
                                class="experience-page__proof-link"
                            >
                                {{ item.title }}
                            </Link>
                        </div>

                        <div class="experience-page__proof-column">
                            <p class="type-nav">{{ copy.writingLabel }}</p>
                            <Link
                                v-for="item in props.latestWriting"
                                :key="item.slug"
                                :href="item.url"
                                class="experience-page__proof-link"
                            >
                                {{ item.title }}
                            </Link>
                        </div>
                    </div>
                </Panel>

                <Panel class="experience-page__proof-card" tone="grid">
                    <p class="type-eyebrow">{{ copy.quickHandoffLabel }}</p>
                    <p class="type-body experience-page__copy-line">
                        {{ copy.quickHandoffSummary }}
                    </p>

                    <div class="experience-page__actions">
                        <Button
                            v-for="download in props.cvDownloads"
                            :key="download.href"
                            :href="download.href"
                        >
                            {{ download.label }}
                        </Button>
                        <Button href="/contact" variant="secondary">
                            {{ copy.contactCta }}
                        </Button>
                    </div>
                </Panel>
            </div>

            <SectionDivider :label="copy.currentPositioningLabel" />

            <div class="experience-page__positioning">
                <Panel class="experience-page__panel" tone="surface">
                    <p class="type-eyebrow">{{ copy.positioningLabel }}</p>

                    <div class="experience-page__copy">
                        <p
                            v-for="paragraph in props.positioning"
                            :key="paragraph"
                            class="type-body experience-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>
                    </div>
                </Panel>

                <Panel class="experience-page__panel" tone="grid">
                    <p class="type-eyebrow">{{ copy.contextsLabel }}</p>

                    <ul class="experience-page__list">
                        <li
                            v-for="context in props.contexts"
                            :key="context"
                            class="experience-page__list-item"
                        >
                            <LegendChip
                                :label="
                                    page.props.site.locale === 'fr'
                                        ? copy.contextChipLabel
                                        : 'Context'
                                "
                                tone="green"
                            />
                            <p class="type-body experience-page__copy-line">
                                {{ context }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>

            <SectionDivider :label="copy.trajectoryLabel" />

            <div class="experience-page__trajectory">
                <Panel
                    v-for="(entry, index) in props.trajectory"
                    :key="entry.title"
                    class="experience-page__trajectory-card"
                    tone="surface"
                >
                    <LegendChip
                        :label="`${copy.phasePrefix} 0${index + 1}`"
                        :tone="trajectoryTones[index] ?? 'violet'"
                    />
                    <h2 class="type-h2 experience-page__title">
                        {{ entry.title }}
                    </h2>
                    <p class="type-body experience-page__copy-line">
                        {{ entry.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider :label="copy.technicalFocusLabel" />

            <div class="experience-page__focus-grid">
                <Panel
                    v-for="focus in props.focusAreas"
                    :key="focus.title"
                    class="experience-page__focus-card"
                    tone="grid"
                >
                    <h2 class="type-h2 experience-page__title">
                        {{ focus.title }}
                    </h2>
                    <p class="type-body experience-page__copy-line">
                        {{ focus.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider :label="copy.strengthsLabel" />

            <div class="experience-page__working-grid">
                <Panel class="experience-page__panel" tone="surface">
                    <p class="type-eyebrow">
                        {{ copy.strengthsWorkingStyleLabel }}
                    </p>

                    <ul class="experience-page__list">
                        <li
                            v-for="strength in props.strengths"
                            :key="strength"
                            class="experience-page__list-item"
                        >
                            <LegendChip
                                :label="
                                    page.props.site.locale === 'fr'
                                        ? copy.strengthChipLabel
                                        : 'Strength'
                                "
                                tone="dominant"
                            />
                            <p class="type-body experience-page__copy-line">
                                {{ strength }}
                            </p>
                        </li>
                    </ul>
                </Panel>

                <Panel class="experience-page__panel" tone="grid">
                    <p class="type-eyebrow">
                        {{ copy.stackEnvironmentsLabel }}
                    </p>

                    <div class="experience-page__stack-groups">
                        <section
                            v-for="group in props.stackGroups"
                            :key="group.title"
                            class="experience-page__stack-group"
                        >
                            <h2 class="type-nav experience-page__stack-title">
                                {{ group.title }}
                            </h2>
                            <div class="experience-page__stack-items">
                                <span
                                    v-for="item in group.items"
                                    :key="item"
                                    class="type-meta experience-page__stack-item"
                                >
                                    {{ item }}
                                </span>
                            </div>
                        </section>
                    </div>
                </Panel>
            </div>

            <SectionDivider :label="copy.whatLookingForLabel" />

            <div class="experience-page__closing-grid">
                <Panel class="experience-page__closing" tone="surface">
                    <p class="type-body experience-page__copy-line">
                        {{ props.lookingFor }}
                    </p>

                    <div class="experience-page__actions">
                        <Button href="/projects">{{ copy.projectsCta }}</Button>
                        <Button href="/case-studies" variant="secondary">
                            {{ copy.caseStudiesCta }}
                        </Button>
                        <Button href="/writing" variant="secondary">
                            {{ copy.writingCta }}
                        </Button>
                        <Button href="/contact" variant="ghost">
                            {{ copy.contactCta }}
                        </Button>
                    </div>
                </Panel>

                <Panel class="experience-page__closing" tone="grid">
                    <p class="type-eyebrow">
                        {{ props.careerSnapshot.title }}
                    </p>
                    <p class="type-body experience-page__copy-line">
                        {{ props.careerSnapshot.summary }}
                    </p>

                    <div class="experience-page__stack-items">
                        <span
                            v-for="role in props.careerSnapshot.roles"
                            :key="role"
                            class="type-meta experience-page__stack-item"
                        >
                            {{ role }}
                        </span>
                    </div>

                    <div class="experience-page__actions">
                        <Button href="/contact">{{ copy.contactCta }}</Button>
                        <Button href="/projects" variant="secondary">
                            {{ copy.projectsCta }}
                        </Button>
                        <Button href="/writing" variant="ghost">
                            {{ copy.openArchiveCta }}
                        </Button>
                    </div>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.experience-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.experience-page__proof-grid,
.experience-page__positioning,
.experience-page__working-grid,
.experience-page__closing-grid {
    display: grid;
    gap: var(--sw-space-sm);
}

.experience-page__proof-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.experience-page__positioning,
.experience-page__working-grid,
.experience-page__closing-grid {
    grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
}

.experience-page__panel,
.experience-page__proof-card,
.experience-page__trajectory-card,
.experience-page__focus-card,
.experience-page__closing {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.experience-page__copy {
    display: grid;
    gap: var(--sw-space-xs);
}

.experience-page__copy-line,
.experience-page__title {
    margin: 0;
}

.experience-page__copy-line {
    color: var(--sw-text-secondary);
}

.experience-page__title {
    color: var(--sw-text-primary);
}

.experience-page__trajectory,
.experience-page__focus-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.experience-page__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.experience-page__list-item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.experience-page__list-item:first-child {
    border-top: 0;
    padding-top: 0;
}

.experience-page__stack-groups {
    display: grid;
    gap: var(--sw-space-sm);
}

.experience-page__proof-columns {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.experience-page__proof-column {
    display: grid;
    gap: var(--sw-space-3xs);
}

.experience-page__proof-link {
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.2em;
}

.experience-page__stack-group {
    display: grid;
    gap: var(--sw-space-xs);
}

.experience-page__stack-title {
    color: var(--sw-text-primary);
}

.experience-page__stack-items {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.experience-page__stack-item {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.experience-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

@media (max-width: 960px) {
    .experience-page__proof-grid,
    .experience-page__positioning,
    .experience-page__working-grid,
    .experience-page__closing-grid,
    .experience-page__trajectory,
    .experience-page__focus-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .experience-page__proof-columns {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
