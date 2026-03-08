<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
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

const heroCapabilities = computed(() =>
    page.props.site.locale === 'fr'
        ? [
              {
                  label: 'Sites marchands',
                  tone: 'violet' as const,
                  details:
                      'WooCommerce / PrestaShop / Shopify / Alokai (ex Vue Storefront)',
                  panelDetails:
                      'WooCommerce / PrestaShop / Shopify / Alokai',
                  summary: props.heroPanel[0] ?? '',
              },
              {
                  label: 'Laravel',
                  tone: 'green' as const,
                  details: 'Laravel / PHP / APIs / CI-CD',
                  panelDetails: 'Laravel / PHP / APIs / CI-CD',
                  summary: props.heroPanel[1] ?? '',
              },
              {
                  label: 'Data produit et SEO',
                  tone: 'sun' as const,
                  details: 'PIM / JSON-LD / Merchant Center / Data layer',
                  panelDetails:
                      'PIM / JSON-LD / Merchant Center / Data layer',
                  summary: props.heroPanel[2] ?? '',
              },
          ]
        : [
              {
                  label: 'E-commerce',
                  tone: 'violet' as const,
                  details:
                      'WooCommerce / PrestaShop / Shopify / Alokai (formerly Vue Storefront)',
                  panelDetails:
                      'WooCommerce / PrestaShop / Shopify / Alokai',
                  summary: props.heroPanel[0] ?? '',
              },
              {
                  label: 'Laravel',
                  tone: 'green' as const,
                  details: 'Laravel / PHP / APIs / CI/CD',
                  panelDetails: 'Laravel / PHP / APIs / CI/CD',
                  summary: props.heroPanel[1] ?? '',
              },
              {
                  label: 'Product data and SEO',
                  tone: 'sun' as const,
                  details: 'PIM / JSON-LD / Merchant Center / Data layer',
                  panelDetails:
                      'PIM / JSON-LD / Merchant Center / Data layer',
                  summary: props.heroPanel[2] ?? '',
              },
          ],
);

const heroAccentChips = computed(() =>
    heroCapabilities.value.map(({ label, tone, details }) => ({
        label,
        tone,
        details,
    })),
);

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Lire les expériences',
              contactCta: 'Prendre contact',
              currentFrameLabel: "Aujourd'hui",
              heroPanelTitle: 'Développeur e-commerce chez Jewely',
              heroPanelSummary:
                  'HBJO, donnée produit, flux, tracking et SEO technique.',
              whatIDoLabel: 'Ce que je fais',
              focusTitle:
                  'Un positionnement net dans des environnements complexes.',
              focusDescription:
                  "Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privée et besoin de garder des systèmes compréhensibles après mise en production.",
              selectedWorkLabel: 'Expérience',
              projectsTitle: 'Quelques références choisies.',
              projectsDescription:
                  "Des cas publics compacts pour montrer comment architecture, donnée produit, tracking, SEO technique et décisions de livraison se traduisent en pratique.",
              openProjectsCta: "Découvrir les projets",
              internalBuildLabel: 'Interne',
              localGroundLabel: 'Où suis-je ?',
              readLocalPageCta: 'Échanger',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Prendre contact',
              referencesCta: 'Lire les expériences',
              archiveCta: "Découvrir les projets",
          }
        : {
              projectsCta: 'View experiences',
              contactCta: 'Start a conversation',
              currentFrameLabel: 'Current role',
              heroPanelTitle: 'E-commerce developer at Jewely',
              heroPanelSummary:
                  'HBJO, product data, integrations, tracking, and technical SEO.',
              whatIDoLabel: 'What I do',
              focusTitle: 'A legible practice for complex environments.',
              focusDescription:
                  'The work usually sits between product delivery, legacy modernization, technical SEO, privacy, and the need to keep systems readable after launch.',
              selectedWorkLabel: 'Experience',
              projectsTitle:
                  'A few build and implementation references.',
              projectsDescription:
                  'A small set of public case studies that show how architecture, product data, tracking, technical SEO, and delivery choices play out in real work.',
              openProjectsCta: 'Open case studies',
              internalBuildLabel: 'Internal build',
              localGroundLabel: 'Local ground',
              readLocalPageCta: 'Read local page',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Start a conversation',
              referencesCta: 'View experiences',
              archiveCta: 'Open case studies',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section sw-section--hero">
            <div class="home-hero">
                <SectionIntro
                    :title="props.hero.title"
                    :description="props.hero.summary"
                    size="hero"
                >
                    <div class="home-hero__support">
                        <div class="home-accent-list">
                            <div
                                v-for="chip in heroAccentChips"
                                :key="chip.label"
                                class="home-accent-chip"
                                :class="[
                                    `home-accent-chip--${chip.tone}`,
                                    `home-tone--${chip.tone}`,
                                ]"
                                tabindex="0"
                            >
                                <LegendChip
                                    :label="chip.label"
                                    :tone="chip.tone"
                                />
                                <span class="home-accent-chip__tooltip">
                                    {{ chip.details }}
                                </span>
                            </div>
                        </div>

                        <div class="home-hero__actions">
                            <Button href="/projects">
                                {{ copy.projectsCta }}
                            </Button>
                            <Button href="/contact" variant="secondary">
                                {{ copy.contactCta }}
                            </Button>
                        </div>
                    </div>
                </SectionIntro>

                <Panel class="home-hero__panel" tone="grid">
                    <p class="type-eyebrow">{{ copy.currentFrameLabel }}</p>
                    <h2 class="home-hero__panel-title">
                        {{ copy.heroPanelTitle }}
                    </h2>
                    <p class="type-body-sm home-hero__panel-summary">
                        {{ copy.heroPanelSummary }}
                    </p>
                    <ul class="home-hero__highlights">
                        <li
                            v-for="item in heroCapabilities"
                            :key="item.label"
                            class="home-hero__highlight"
                            :class="`home-tone--${item.tone}`"
                        >
                            <LegendChip :label="item.label" tone="sun" />
                            <p
                                class="type-meta home-hero__highlight-details"
                                :class="`home-hero__highlight-details--${item.tone}`"
                            >
                                {{ item.panelDetails }}
                            </p>
                            <p class="type-body-sm home-hero__highlight-copy">
                                {{ item.summary }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>
        </section>

        <section class="sw-section sw-section--flow home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.whatIDoLabel"
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
                    <Button :href="focus.href" variant="ghost" arrow>
                        {{ focus.cta }}
                    </Button>
                </Panel>
            </div>
        </section>

        <section class="sw-section sw-section--flow home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.selectedWorkLabel"
                    :title="copy.projectsTitle"
                    :description="copy.projectsDescription"
                />
                <Button href="/case-studies" variant="ghost" arrow>
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

        <section class="sw-section sw-section--flow home-section">
            <div class="home-section__header">
                <SectionIntro
                    :eyebrow="copy.localGroundLabel"
                    :title="props.localTeaser.title"
                    :description="props.localTeaser.summary"
                />
                <Button href="/contact" variant="ghost" arrow>
                    {{ copy.readLocalPageCta }}
                </Button>
            </div>
        </section>

        <PublicationWidget
            :widget="props.journalWidget"
            tone="surface"
            class="sw-section--flow home-section home-journal-widget"
        />

        <section class="sw-section sw-section--flow home-section">
            <Panel class="home-contact" tone="surface">
                <div class="home-contact__copy">
                    <p class="type-eyebrow home-contact__eyebrow">
                        {{ copy.contactLabel }}
                    </p>
                    <h2 class="type-h2 home-contact__title">
                        {{ props.contactCta.title }}
                    </h2>
                    <p class="type-body home-contact__summary">
                        {{ props.contactCta.summary }}
                    </p>
                </div>

                <div class="home-contact__actions">
                    <div class="home-contact__downloads">
                        <Button
                            v-for="download in props.cvDownloads"
                            :key="download.href"
                            :href="download.href"
                            variant="ghost"
                        >
                            {{ download.label }}
                        </Button>
                    </div>
                    <div class="home-contact__cta-row">
                        <Button href="/contact">{{
                            copy.startConversationCta
                        }}</Button>
                        <Button href="/projects" variant="secondary">
                            {{ copy.referencesCta }}
                        </Button>
                    </div>
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

.home-hero__support {
    display: grid;
    gap: 0.85rem;
}

.home-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.home-accent-chip {
    position: relative;
    display: inline-flex;
    width: fit-content;
    outline: none;
    --home-accent-color: var(--sw-accent-dominant);
}

.home-accent-chip__tooltip {
    position: absolute;
    left: 0;
    bottom: calc(100% + 10px);
    z-index: 2;
    min-width: max-content;
    max-width: min(22rem, 80vw);
    border: 1px solid color-mix(in srgb, var(--sw-border) 86%, transparent);
    border-radius: calc(var(--sw-radius-md) + 2px);
    background: color-mix(in srgb, var(--sw-bg-elevated) 94%, transparent);
    padding: 0.58rem 0.72rem;
    color: var(--sw-text-primary);
    font-family: var(--sw-font-body);
    font-size: 0.79rem;
    font-weight: 500;
    line-height: 1.35;
    white-space: nowrap;
    box-shadow: var(--sw-shadow-md);
    opacity: 0;
    pointer-events: none;
    transform: translateY(4px);
    transition:
        opacity var(--sw-motion-fast),
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.home-accent-chip__tooltip::after {
    content: '';
    position: absolute;
    left: 16px;
    top: calc(100% - 1px);
    width: 10px;
    height: 10px;
    border-right: 1px solid currentColor;
    border-bottom: 1px solid currentColor;
    transform: rotate(45deg);
    opacity: 0.18;
    background: inherit;
}

.home-tone--violet {
    --home-accent-color: #6b33c8;
}

.home-tone--green {
    --home-accent-color: #87d63f;
}

.home-tone--sun {
    --home-accent-color: #c98714;
}

.home-accent-chip--violet .home-accent-chip__tooltip,
.home-accent-chip--green .home-accent-chip__tooltip,
.home-accent-chip--sun .home-accent-chip__tooltip {
    border-color: color-mix(in srgb, var(--home-accent-color) 28%, var(--sw-border));
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, var(--home-accent-color) 12%);
}

.home-accent-chip--violet :deep(.legend-chip),
.home-accent-chip--green :deep(.legend-chip),
.home-accent-chip--sun :deep(.legend-chip) {
    --chip-accent: var(--home-accent-color);
    color: var(--home-accent-color);
}

.home-accent-chip:hover .home-accent-chip__tooltip,
.home-accent-chip:focus-within .home-accent-chip__tooltip,
.home-accent-chip:focus .home-accent-chip__tooltip {
    opacity: 1;
    transform: translateY(0);
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

.home-hero__panel-title {
    font-family: var(--sw-font-body);
    font-size: clamp(1rem, 1.25vw, 1.12rem);
    font-weight: 600;
    line-height: 1.35;
    color: color-mix(in srgb, var(--sw-text-primary) 82%, var(--sw-text-secondary));
}

.home-hero__panel-summary {
    margin: -0.3rem 0 0;
    color: var(--sw-text-secondary);
    line-height: 1.45;
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

.home-hero__highlight {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.home-hero__highlight:first-child {
    border-top: 0;
    padding-top: 0;
}

.home-hero__highlight-copy,
.home-focus-card__summary,
.home-card__summary,
.home-contact__summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-hero__highlight-details {
    margin: calc(var(--sw-space-3xs) * -0.35) 0 0;
    font-family: var(--sw-font-body);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    color: var(--home-accent-color);
}

.home-hero__highlight :deep(.legend-chip) {
    --chip-accent: var(--sw-accent-sun);
    color: var(--sw-accent-sun);
}

.home-card__role {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-section {
    display: grid;
    gap: clamp(var(--sw-space-xs), 2vw, var(--sw-space-sm));
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
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
    border-radius: inherit;
}

.home-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.home-card__tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.35rem;
    color: var(--sw-accent-dominant);
}

.home-contact {
    grid-template-areas:
        'copy'
        'actions';
    align-items: start;
    padding-block: clamp(10px, 1.6vw, 16px);
}

.home-contact__copy {
    grid-area: copy;
    display: grid;
    gap: 0;
    max-width: 48rem;
}

.home-contact__eyebrow {
    width: fit-content;
    margin: 0;
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, transparent);
    font-size: 10px;
    letter-spacing: 0.16em;
}

.home-contact__title {
    padding-block: 14px 10px;
    color: color-mix(in srgb, var(--sw-text-primary) 76%, var(--sw-text-secondary));
}

.home-contact__summary {
    max-width: 60ch;
    line-height: 1.34;
}

.home-contact__actions {
    grid-area: actions;
    display: grid;
    gap: var(--sw-space-xs);
    justify-items: end;
    align-self: end;
    align-content: end;
}

.home-contact__downloads,
.home-contact__cta-row {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    justify-content: flex-end;
}

.home-contact__downloads :deep(.sw-button) {
    min-height: 2.5rem;
    white-space: nowrap;
}

.home-card-link:focus-visible {
    outline: none;
}

.home-card-link:focus-visible .home-card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.home-card-link:active .home-card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .home-card-link:hover .home-card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        box-shadow: var(--sw-shadow-md);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    }
}

@media (min-width: 960px) {
    .home-hero {
        grid-template-columns: minmax(0, 1.45fr) minmax(18rem, 0.85fr);
    }

    .home-contact {
        grid-template-areas: 'copy actions';
        grid-template-columns: minmax(0, 1.35fr) minmax(24rem, 26rem);
        align-items: stretch;
    }

    .home-contact__actions {
        min-height: 100%;
    }

    .home-contact__downloads,
    .home-contact__cta-row {
        flex-wrap: nowrap;
    }
}

@media (max-width: 960px) {
    .home-focus-grid,
    .home-card-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .home-contact {
        grid-template-columns: minmax(0, 1fr);
    }

    .home-contact__actions {
        justify-items: stretch;
    }

    .home-contact__downloads,
    .home-contact__cta-row {
        justify-content: flex-end;
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

    .home-accent-list,
    .home-hero__actions {
        gap: var(--sw-space-3xs);
    }

    .home-hero__actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
    }

    .home-hero__actions :deep(.sw-button) {
        width: 100%;
    }

    .home-accent-chip__tooltip {
        max-width: min(18rem, 76vw);
        white-space: normal;
    }
}
</style>
