<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
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
    journalWidget: PublicationWidgetData;
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
              projectsCta: 'Voir les références',
              contactCta: 'Prendre contact',
              currentFrameLabel: 'Cadre actuel',
              heroPanelTitle:
                  'Catalogues, SEO technique et architecture produit dans des environnements qui ont déjà une histoire.',
              whatIDoLabel: 'Ce que je fais',
              focusEyebrow: 'Focus',
              focusTitle:
                  'Un positionnement net dans des environnements complexes.',
              focusDescription:
                  "Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privée et besoin de garder des systèmes compréhensibles après mise en production.",
              selectedWorkLabel: 'Références',
              projectsEyebrow: 'Références',
              projectsTitle: 'Quelques références choisies.',
              projectsDescription:
                  "Des cas publics compacts pour montrer comment architecture, catalogue, SEO technique et décisions de livraison se traduisent en pratique.",
              openProjectsCta: "Voir les cas d'étude",
              internalBuildLabel: 'Interne',
              localGroundLabel: 'Ancrage local',
              localEyebrow: 'Ancrage local',
              readLocalPageCta: 'Lire la page locale',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Prendre contact',
              referencesCta: 'Voir la surface de travail',
              archiveCta: "Voir les cas d'étude",
              heroChipCommerceLabel: 'Sites marchands',
              heroChipFrameworkLabel: 'Laravel',
              heroChipSeoLabel: 'Catalogue et SEO',
              signalPrefix: 'Signal',
              localPointPrefix: 'Point',
          }
        : {
              projectsCta: 'Browse references',
              contactCta: 'Start a conversation',
              currentFrameLabel: 'Current frame',
              heroPanelTitle:
                  'Catalog quality, technical SEO, and product architecture in environments that already have history.',
              whatIDoLabel: 'What I do',
              focusEyebrow: 'Focus',
              focusTitle: 'A legible practice for complex environments.',
              focusDescription:
                  'The work usually sits between product delivery, legacy modernization, technical SEO, privacy, and the need to keep systems readable after launch.',
              selectedWorkLabel: 'References',
              projectsEyebrow: 'References',
              projectsTitle:
                  'A few build and implementation references.',
              projectsDescription:
                  'A small set of public case studies that show how architecture, catalog work, technical SEO, and delivery choices play out in real work.',
              openProjectsCta: 'Open case studies',
              internalBuildLabel: 'Internal build',
              localGroundLabel: 'Local ground',
              readLocalPageCta: 'Read local page',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Start a conversation',
              referencesCta: 'Open work surface',
              archiveCta: 'Open case studies',
              heroChipCommerceLabel: 'E-commerce',
              heroChipFrameworkLabel: 'Laravel',
              heroChipSeoLabel: 'Catalogs and SEO',
              localEyebrow: 'Local',
              signalPrefix: 'Signal',
              localPointPrefix: 'Point',
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

                    <div class="home-accent-list">
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
                                    : 'Catalogs and SEO'
                            "
                            tone="sun"
                        />
                    </div>
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
                                :label="`${page.props.site.locale === 'fr' ? copy.signalPrefix : 'Signal'} 0${index + 1}`"
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
                    <LegendChip
                        :label="focus.label"
                        :tone="focus.tone"
                        class="home-focus-card__label"
                    />
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
                <Button href="/case-studies" variant="ghost">
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
                        <ContentVisual :item="item" compact />
                        <LegendChip
                            :label="item.client || copy.internalBuildLabel"
                            tone="green"
                            class="home-card__eyebrow"
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
                    :eyebrow="
                        page.props.site.locale === 'fr'
                            ? copy.localEyebrow
                            : 'Local'
                    "
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
                            :label="`${page.props.site.locale === 'fr' ? copy.localPointPrefix : 'Point'} 0${index + 1}`"
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

        <PublicationWidget :widget="props.journalWidget" tone="surface" />

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
                    <Button href="/projects" variant="secondary">
                        {{ copy.referencesCta }}
                    </Button>
                    <Button
                        v-for="download in props.cvDownloads"
                        :key="download.href"
                        :href="download.href"
                        variant="ghost"
                    >
                        {{ download.label }}
                    </Button>
                    <Button href="/case-studies" variant="ghost">
                        {{ copy.archiveCta }}
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
.home-local,
.home-contact {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.home-accent-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem 0.9rem;
}

.home-focus-card__label,
.home-card__eyebrow {
    width: fit-content;
}

.home-hero__panel-title,
.home-focus-card__title,
.home-card__title,
.home-contact__title {
    margin: 0;
    color: var(--sw-text-primary);
}

.home-hero__highlights,
.home-focus-grid {
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
    gap: var(--sw-space-xs);
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

.home-card-link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.home-card {
    position: relative;
    display: grid;
    height: 100%;
    overflow: hidden;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
    border-radius: inherit;
}

.home-card::before {
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
    background: linear-gradient(
        90deg,
        var(--sw-accent-dominant),
        color-mix(in srgb, var(--sw-accent-sun) 68%, var(--sw-accent-dominant))
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
    min-height: 1.35rem;
    color: var(--sw-accent-dominant);
}

.home-contact {
    align-items: end;
}

.home-contact__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.home-card-link:focus-visible {
    outline: none;
}

.home-card-link:focus-visible .home-card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.home-card-link:focus-visible .home-card::before,
.home-card-link:active .home-card::before {
    transform: scaleX(1);
    opacity: 1;
}

.home-card-link:active .home-card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .home-card-link:hover .home-card {
        transform: translateY(-2px);
        box-shadow: var(--sw-shadow-md);
    }

    .home-card-link:hover .home-card {
        border-color: var(--sw-accent-dominant);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    }

    .home-card-link:hover .home-card::before {
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
}
</style>
