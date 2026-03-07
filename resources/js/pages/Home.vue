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
    heroPanel: string[];
    focusAreas: Array<{
        label: string;
        title: string;
        summary: string;
        href: string;
        cta: string;
        tone: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
    }>;
    featuredCaseStudies: ContentItem[];
    latestWriting: ContentItem[];
    localTeaser: {
        title: string;
        summary: string;
        points: string[];
    };
    contactCta: {
        title: string;
        summary: string;
    };
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
}>();

const heroPanelTones = ['dominant', 'green', 'coral'] as const;

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Voir preuves',
              experienceCta: 'Lire le parcours',
              contactCta: 'Entrer en contact',
              currentFrameLabel: 'Cadre actuel',
              heroPanelTitle:
                  'Maintenabilite et preuve dans des environnements qui ont deja une histoire.',
              whatIDoLabel: 'Ce que je fais',
              focusEyebrow: 'Focus',
              focusTitle:
                  'Une pratique lisible dans des environnements complexes.',
              focusDescription:
                  'Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privee et besoin de garder les systemes comprehensibles apres mise en prod.',
              selectedWorkLabel: 'Preuves publiques',
              projectsEyebrow: 'Projets',
              projectsTitle:
                  "Tranches de projet et preuves d'implementation selectionnees.",
              projectsDescription:
                  'Un petit ensemble de cas clients publics qui montrent comment architecture, maintenabilite et decisions de livraison se traduisent en pratique.',
              openProjectsCta: 'Ouvrir projets',
              internalBuildLabel: 'Build interne',
              localGroundLabel: 'Ancrage local',
              readLocalPageCta: 'Lire la page locale',
              notesLabel: 'Notes',
              writingEyebrow: 'Notes',
              writingTitle: 'Notes issues de la construction du systeme',
              writingDescription:
                  "Pieces courtes sur l'architecture, les systemes de contenu, la logique de consentement et les decisions pratiques derriere des produits durables.",
              openArchiveCta: "Ouvrir l'archive",
              writingLabel: 'Note',
              publishedSeparator: '·',
              contactLabel: 'Contact',
              startConversationCta: 'Commencer la conversation',
              proofPackLabel: 'CV et preuves',
          }
        : {
              projectsCta: 'View proof',
              experienceCta: 'Read experience',
              contactCta: 'Start a conversation',
              currentFrameLabel: 'Current frame',
              heroPanelTitle:
                  'Maintainability and proof in environments that already have history.',
              whatIDoLabel: 'What I do',
              focusEyebrow: 'Focus',
              focusTitle: 'A legible practice for complex environments.',
              focusDescription:
                  'The work usually sits between product delivery, legacy modernization, technical SEO, privacy, and the need to keep systems readable after launch.',
              selectedWorkLabel: 'Selected work',
              projectsEyebrow: 'Projects',
              projectsTitle:
                  'Selected project slices and implementation proof.',
              projectsDescription:
                  'A small set of public case studies that show how architecture, maintainability, and delivery choices play out in real work.',
              openProjectsCta: 'Open projects',
              internalBuildLabel: 'Internal build',
              localGroundLabel: 'Local ground',
              readLocalPageCta: 'Read local page',
              notesLabel: 'Notes',
              writingEyebrow: 'Writing',
              writingTitle: 'Notes from the system build',
              writingDescription:
                  'Short pieces about architecture, content systems, consent logic, and the practical decisions behind durable products.',
              openArchiveCta: 'Open archive',
              writingLabel: 'Writing',
              publishedSeparator: '·',
              contactLabel: 'Contact',
              startConversationCta: 'Start a conversation',
              proofPackLabel: 'CV and proof pack',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section sw-section--hero">
            <div class="home-hero">
                <SectionIntro
                    :eyebrow="props.hero.eyebrow"
                    :title="props.hero.title"
                    :description="props.hero.summary"
                    size="hero"
                >
                    <template #actions>
                        <Button href="/projects">{{ copy.projectsCta }}</Button>
                        <Button href="/contact" variant="secondary">
                            {{ copy.contactCta }}
                        </Button>
                    </template>

                    <LegendChip label="E-commerce" tone="dominant" />
                    <LegendChip label="Laravel" tone="green" />
                    <LegendChip label="Privacy-aware systems" tone="sun" />
                </SectionIntro>

                <Panel class="home-hero__panel" tone="grid">
                    <p class="type-eyebrow">{{ copy.currentFrameLabel }}</p>
                    <h2 class="type-h2 home-hero__panel-title">
                        {{ copy.heroPanelTitle }}
                    </h2>
                    <ul class="home-hero__highlights">
                        <li
                            v-for="(highlight, index) in props.heroPanel"
                            :key="highlight"
                            class="home-hero__highlight"
                        >
                            <LegendChip
                                :label="`Signal 0${index + 1}`"
                                :tone="heroPanelTones[index] ?? 'violet'"
                            />
                            <p class="type-body-sm home-hero__highlight-copy">
                                {{ highlight }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>
        </section>

        <SectionDivider :label="copy.whatIDoLabel" />

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.focusEyebrow"
                    :title="copy.focusTitle"
                    :description="copy.focusDescription"
                />
            </div>

            <div class="home-focus-grid">
                <Panel
                    v-for="focus in props.focusAreas"
                    :key="focus.title"
                    class="home-focus-card"
                    tone="surface"
                >
                    <LegendChip :label="focus.label" :tone="focus.tone" />
                    <h2 class="type-h2 home-focus-card__title">
                        {{ focus.title }}
                    </h2>
                    <p class="type-body home-focus-card__summary">
                        {{ focus.summary }}
                    </p>
                    <Button :href="focus.href" variant="ghost">
                        {{ focus.cta }}
                    </Button>
                </Panel>
            </div>
        </section>

        <SectionDivider :label="copy.selectedWorkLabel" />

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.projectsEyebrow"
                    :title="copy.projectsTitle"
                    :description="copy.projectsDescription"
                />
                <Button href="/projects" variant="ghost">
                    {{ copy.openProjectsCta }}
                </Button>
            </div>

            <div class="home-card-grid">
                <Link
                    v-for="item in props.featuredCaseStudies"
                    :key="item.slug"
                    :href="item.url"
                    class="home-card-link"
                >
                    <Panel class="home-card" tone="surface">
                        <LegendChip
                            :label="item.client || copy.internalBuildLabel"
                            tone="green"
                        />
                        <h3 class="type-h2 home-card__title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body-sm home-card__role">
                            {{ item.role }}
                        </p>
                        <p class="type-body home-card__summary">
                            {{ item.summary }}
                        </p>
                        <div class="home-card__meta">
                            <span
                                v-for="tag in item.tags.slice(0, 3)"
                                :key="tag"
                                class="type-meta home-card__tag"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </Panel>
                </Link>
            </div>
        </section>

        <SectionDivider :label="copy.localGroundLabel" />

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    eyebrow="Local"
                    :title="props.localTeaser.title"
                    :description="props.localTeaser.summary"
                />
                <Button href="/local" variant="ghost">
                    {{ copy.readLocalPageCta }}
                </Button>
            </div>

            <Panel class="home-local" tone="grid">
                <div class="home-local__points">
                    <article
                        v-for="(point, index) in props.localTeaser.points"
                        :key="point"
                        class="home-local__point"
                    >
                        <LegendChip
                            :label="`Point 0${index + 1}`"
                            :tone="heroPanelTones[index] ?? 'violet'"
                        />
                        <p class="type-body-sm home-local__copy">
                            {{ point }}
                        </p>
                    </article>
                </div>
            </Panel>
        </section>

        <SectionDivider :label="copy.notesLabel" />

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.writingEyebrow"
                    :title="copy.writingTitle"
                    :description="copy.writingDescription"
                />
                <Button href="/writing" variant="ghost">
                    {{ copy.openArchiveCta }}
                </Button>
            </div>

            <div class="home-writing-list">
                <Link
                    v-for="item in props.latestWriting"
                    :key="item.slug"
                    :href="item.url"
                    class="home-writing-link"
                >
                    <Panel class="home-writing-card" tone="surface">
                        <div class="home-writing-card__meta">
                            <LegendChip
                                :label="copy.writingLabel"
                                tone="violet"
                            />
                            <span class="type-meta">
                                {{ item.published_at }}
                                {{ copy.publishedSeparator }}
                                {{ item.reading_time }} min
                            </span>
                        </div>
                        <h3 class="type-h2 home-writing-card__title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body home-writing-card__summary">
                            {{ item.summary }}
                        </p>
                    </Panel>
                </Link>
            </div>
        </section>

        <SectionDivider :label="copy.contactLabel" />

        <section class="sw-section home-section">
            <Panel class="home-contact" tone="surface">
                <div class="home-contact__copy">
                    <p class="type-eyebrow">{{ copy.contactLabel }}</p>
                    <h2 class="type-h2 home-contact__title">
                        {{ props.contactCta.title }}
                    </h2>
                    <p class="type-body home-contact__summary">
                        {{ props.contactCta.summary }}
                    </p>
                </div>

                <div class="home-contact__actions">
                    <Button href="/contact">{{
                        copy.startConversationCta
                    }}</Button>
                    <Button href="/experience" variant="secondary">
                        {{ copy.experienceCta }}
                    </Button>
                    <Button
                        v-for="download in props.cvDownloads"
                        :key="download.href"
                        :href="download.href"
                        variant="ghost"
                    >
                        {{ download.label }}
                    </Button>
                    <Button href="/projects" variant="ghost">
                        {{ copy.proofPackLabel }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.home-hero {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
}

.home-hero__panel,
.home-focus-card,
.home-card,
.home-writing-card,
.home-local,
.home-contact {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.home-hero__panel-title,
.home-focus-card__title,
.home-card__title,
.home-writing-card__title,
.home-contact__title {
    margin: 0;
    color: var(--sw-text-primary);
}

.home-hero__highlights,
.home-focus-grid,
.home-writing-list {
    display: grid;
    gap: var(--sw-space-sm);
}

.home-hero__highlights {
    margin: 0;
    padding: 0;
    list-style: none;
}

.home-hero__highlight,
.home-local__point {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.home-hero__highlight:first-child,
.home-local__point:first-child {
    border-top: 0;
    padding-top: 0;
}

.home-hero__highlight-copy,
.home-focus-card__summary,
.home-card__summary,
.home-writing-card__summary,
.home-local__copy,
.home-contact__summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-card__role {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-section {
    display: grid;
    gap: var(--sw-space-sm);
}

.home-section__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
    align-items: end;
    justify-content: space-between;
}

.home-section__header :deep(.section-intro) {
    max-width: 46rem;
}

.home-focus-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.home-card-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.home-card-link,
.home-writing-link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.home-card,
.home-writing-card {
    position: relative;
    height: 100%;
    overflow: hidden;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.home-card::before,
.home-writing-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 3px;
    transform: scaleX(0.24);
    transform-origin: left center;
    opacity: 0;
    transition:
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
}

.home-card::before {
    background: linear-gradient(
        90deg,
        var(--sw-accent-dominant),
        color-mix(in srgb, var(--sw-accent-sun) 68%, var(--sw-accent-dominant))
    );
}

.home-writing-card::before {
    background: linear-gradient(
        90deg,
        var(--sw-accent-violet),
        color-mix(in srgb, var(--sw-accent-sun) 52%, var(--sw-accent-violet))
    );
}

.home-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.home-local__points {
    display: grid;
    gap: var(--sw-space-xs);
}

.home-card__tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 76%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.home-writing-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
    justify-content: space-between;
}

.home-contact {
    align-items: end;
}

.home-contact__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.home-card-link:focus-visible,
.home-writing-link:focus-visible {
    outline: none;
}

.home-card-link:focus-visible .home-card,
.home-writing-link:focus-visible .home-writing-card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.home-card-link:focus-visible .home-card::before,
.home-writing-link:focus-visible .home-writing-card::before,
.home-card-link:active .home-card::before,
.home-writing-link:active .home-writing-card::before {
    transform: scaleX(1);
    opacity: 1;
}

.home-card-link:active .home-card,
.home-writing-link:active .home-writing-card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .home-card-link:hover .home-card,
    .home-writing-link:hover .home-writing-card {
        transform: translateY(-2px);
        box-shadow: var(--sw-shadow-md);
    }

    .home-card-link:hover .home-card {
        border-color: var(--sw-accent-dominant);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    }

    .home-writing-link:hover .home-writing-card {
        border-color: var(--sw-accent-violet);
        background: color-mix(in srgb, var(--sw-bg-elevated) 86%, transparent);
    }

    .home-card-link:hover .home-card::before,
    .home-writing-link:hover .home-writing-card::before {
        transform: scaleX(1);
        opacity: 1;
    }
}

@media (min-width: 960px) {
    .home-hero {
        grid-template-columns: minmax(0, 1.45fr) minmax(18rem, 0.85fr);
    }

    .home-contact {
        grid-template-columns: minmax(0, 1fr) auto;
    }
}

@media (max-width: 960px) {
    .home-focus-grid,
    .home-card-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .home-hero,
    .home-section {
        gap: var(--sw-space-xs);
    }

    .home-section__header {
        gap: var(--sw-space-xs);
        align-items: start;
    }

    .home-writing-card__meta {
        justify-content: flex-start;
    }
}
</style>
