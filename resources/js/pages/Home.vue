<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import PublicationWidget from '@/components/content/PublicationWidget.vue';
import InlineTermTooltip from '@/components/design-system/InlineTermTooltip.vue';
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

const heroCapabilities = computed(() =>
    page.props.site.locale === 'fr'
        ? [
              {
                  label: 'Sites marchands',
                  tone: 'violet' as const,
                  details:
                      'WooCommerce / PrestaShop / Shopify / Alokai (ex Vue Storefront)',
                  panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
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
                  panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
                  summary: props.heroPanel[2] ?? '',
              },
          ]
        : [
              {
                  label: 'E-commerce',
                  tone: 'violet' as const,
                  details:
                      'WooCommerce / PrestaShop / Shopify / Alokai (formerly Vue Storefront)',
                  panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
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
                  panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
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

const heroLeadPoints = computed(() =>
    props.heroPanel.filter((point) => point.trim() !== '').slice(0, 3),
);

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              projectsCta: 'Lire les expériences',
              contactCta: 'Prendre contact',
              currentFrameLabel: "Aujourd'hui",
              heroPanelTitle: 'Développeur e-commerce chez Jewely / Flippad',
              heroPanelSummarySuffix:
                  'écosystème HBJO, ERP, PIM, flux produit, tracking et SEO technique.',
              hbjoatDefinition:
                  'Horlogerie, bijouterie, joaillerie et orfèvrerie.',
              cmsDefinition:
                  'Content Management System : système de gestion de contenu.',
              phpDefinition:
                  'PHP : langage serveur largement utilisé pour les applications web et e-commerce.',
              apiDefinition:
                  "API : interface d'échange entre services, outils métier et applications.",
              ciCdDefinition:
                  'CI/CD : intégration et déploiement continus pour fiabiliser les mises en ligne.',
              seoDefinition:
                  'SEO : optimisation technique et éditoriale pour rendre un site lisible par les moteurs et utile aux visiteurs.',
              pimDefinition:
                  'PIM : Product Information Management, le socle qui centralise et structure la donnée produit.',
              jsonLdDefinition:
                  'JSON-LD : format de données structurées lisible par les moteurs et les plateformes.',
              merchantCenterDefinition:
                  'Google Merchant Center : flux catalogue et diffusion produit vers les surfaces shopping Google.',
              dataLayerDefinition:
                  'Data layer : couche de données partagée entre le site, le tracking et les outils marketing.',
              whatIDoLabel: 'Ce que je fais',
              focusTitle:
                  'Un positionnement net dans des environnements complexes.',
              focusDescription:
                  'Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privée et besoin de garder des systèmes compréhensibles après mise en production.',
              selectedWorkLabel: 'Expérience',
              projectsTitle: 'Études de cas et repères à ouvrir ensuite.',
              projectsDescription:
                  'Études de cas, notes et références pour entrer dans des situations plus concrètes.',
              openProjectsCta: 'Découvrir les projets',
              internalBuildLabel: 'Interne',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Prendre contact',
              referencesCta: 'Lire les expériences',
              archiveCta: 'Découvrir toutes les études de cas',
          }
        : {
              projectsCta: 'View experiences',
              contactCta: 'Start a conversation',
              currentFrameLabel: 'Current role',
              heroPanelTitle: 'E-commerce developer at Jewely / Flippad',
              heroPanelSummarySuffix:
                  'HBJO commerce, ERP, PIM, product flows, tracking, and technical SEO.',
              hbjoatDefinition:
                  'Watchmaking, jewelry, silverware, and tableware.',
              cmsDefinition: 'CMS: Content Management System.',
              phpDefinition:
                  'PHP: a server-side language widely used for web and e-commerce applications.',
              apiDefinition:
                  'API: an interface used to connect services, business tools, and applications.',
              ciCdDefinition:
                  'CI/CD: continuous integration and delivery practices that make releases safer.',
              seoDefinition:
                  'SEO: technical and editorial optimization that helps a site stay legible for search engines and useful for people.',
              pimDefinition:
                  'PIM: Product Information Management, the layer that centralizes and structures product data.',
              jsonLdDefinition:
                  'JSON-LD: a structured-data format understood by search engines and platforms.',
              merchantCenterDefinition:
                  'Google Merchant Center: product feed distribution across Google shopping surfaces.',
              dataLayerDefinition:
                  'Data layer: the shared data layer used by the site, tracking, and marketing tools.',
              whatIDoLabel: 'What I do',
              focusTitle: 'A legible practice for complex environments.',
              focusDescription:
                  'The work usually sits between product delivery, legacy modernization, technical SEO, privacy, and the need to keep systems readable after launch.',
              selectedWorkLabel: 'Experience',
              projectsTitle: 'Case studies and pointers worth opening next.',
              projectsDescription:
                  'Case studies, notes, and references that open more concrete implementation contexts.',
              openProjectsCta: 'Open case studies',
              internalBuildLabel: 'Internal build',
              notesLabel: 'Notes',
              contactLabel: 'Contact',
              startConversationCta: 'Start a conversation',
              referencesCta: 'View experiences',
              archiveCta: 'Browse all case studies',
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
                        <ul
                            v-if="heroLeadPoints.length"
                            class="home-hero__lead-points"
                        >
                            <li
                                v-for="point in heroLeadPoints"
                                :key="point"
                                class="home-hero__lead-point"
                            >
                                <span
                                    class="home-hero__lead-bullet"
                                    aria-hidden="true"
                                />
                                <span class="type-body home-hero__lead-copy">
                                    {{ point }}
                                </span>
                            </li>
                        </ul>

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
                        <InlineTermTooltip
                            :label="
                                page.props.site.locale === 'fr'
                                    ? 'HBJO'
                                    : 'HBJOAT'
                            "
                            :definition="copy.hbjoatDefinition"
                            tone="green"
                        />
                        {{ ', ' }}{{ copy.heroPanelSummarySuffix }}
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
                                <template v-if="item.label === 'Laravel'">
                                    <InlineTermTooltip
                                        label="Laravel"
                                        :definition="
                                            page.props.site.locale === 'fr'
                                                ? 'Framework PHP pour applications web modernes.'
                                                : 'PHP framework for modern web applications.'
                                        "
                                        tone="green"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="PHP"
                                        :definition="copy.phpDefinition"
                                        tone="green"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="API"
                                        :definition="copy.apiDefinition"
                                        tone="green"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="CI/CD"
                                        :definition="copy.ciCdDefinition"
                                        tone="green"
                                    />
                                </template>
                                <template
                                    v-else-if="
                                        item.label === 'Data produit et SEO' ||
                                        item.label === 'Product data and SEO'
                                    "
                                >
                                    <InlineTermTooltip
                                        label="PIM"
                                        :definition="copy.pimDefinition"
                                        tone="sun"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="JSON-LD"
                                        :definition="copy.jsonLdDefinition"
                                        tone="sun"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="Merchant Center"
                                        :definition="
                                            copy.merchantCenterDefinition
                                        "
                                        tone="sun"
                                    />
                                    {{ ' / ' }}
                                    <InlineTermTooltip
                                        label="Data layer"
                                        :definition="copy.dataLayerDefinition"
                                        tone="sun"
                                    />
                                </template>
                                <template v-else>
                                    {{ item.panelDetails }}
                                </template>
                            </p>
                            <p class="type-body-sm home-hero__highlight-copy">
                                <template
                                    v-if="item.label === 'Sites marchands'"
                                >
                                    Développement e-commerce et
                                    <InlineTermTooltip
                                        label="CMS"
                                        :definition="copy.cmsDefinition"
                                        tone="violet"
                                    />
                                    sur WooCommerce, PrestaShop, Shopify et
                                    fronts découplés quand le projet le demande.
                                </template>
                                <template
                                    v-else-if="item.label === 'E-commerce'"
                                >
                                    E-commerce and
                                    <InlineTermTooltip
                                        label="CMS"
                                        :definition="copy.cmsDefinition"
                                        tone="violet"
                                    />
                                    development across WooCommerce, PrestaShop,
                                    Shopify, and decoupled fronts when needed.
                                </template>
                                <template
                                    v-else-if="
                                        item.label === 'Data produit et SEO'
                                    "
                                >
                                    <InlineTermTooltip
                                        label="SEO"
                                        :definition="copy.seoDefinition"
                                        tone="sun"
                                    />
                                    technique, données structurées, flux
                                    produit, PIM, catalogues marketing, tracking
                                    et data layer au même niveau que la mise en
                                    ligne.
                                </template>
                                <template
                                    v-else-if="
                                        item.label === 'Product data and SEO'
                                    "
                                >
                                    <InlineTermTooltip
                                        label="SEO"
                                        :definition="copy.seoDefinition"
                                        tone="sun"
                                    />
                                    , structured data, product feeds, PIM,
                                    marketing catalogs, tracking, and data layer
                                    handled at the same level as go-live.
                                </template>
                                <template v-else>
                                    {{ item.summary }}
                                </template>
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
                        <div class="home-card__body">
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
                        </div>
                    </Panel>
                </Link>
            </div>

            <div class="home-section__footer-cta">
                <Button href="/case-studies" variant="ghost" arrow>
                    {{ copy.archiveCta }}
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
    gap: var(--sw-space-xs);
    padding: clamp(16px, 2.2vw, 20px);
}

.home-accent-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem 0.9rem;
}

.home-hero__support {
    display: grid;
    gap: 0.85rem;
    min-width: 0;
}

.home-hero__lead-points {
    display: grid;
    gap: 0.65rem;
    margin: 0;
    padding: 0;
    list-style: none;
    max-width: 48rem;
}

.home-hero__lead-point {
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
}

.home-hero__lead-bullet {
    width: 0.42rem;
    height: 0.42rem;
    margin-top: 0.52rem;
    flex: none;
    border-radius: 999px;
    background: color-mix(
        in srgb,
        var(--sw-accent-sun) 72%,
        var(--sw-accent-dominant)
    );
}

.home-hero__lead-copy {
    margin: 0;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 82%,
        var(--sw-text-secondary)
    );
    line-height: 1.48;
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
    max-width: 100%;
    outline: none;
    --home-accent-color: var(--sw-accent-dominant);
}

.home-accent-chip__tooltip {
    position: absolute;
    left: 0;
    bottom: calc(100% + 10px);
    z-index: 2;
    width: max-content;
    min-width: min(14rem, calc(100vw - 2rem));
    max-width: min(22rem, calc(100vw - 2rem));
    border: 1px solid color-mix(in srgb, var(--sw-border) 86%, transparent);
    border-radius: 4px;
    background: color-mix(in srgb, var(--sw-bg-elevated) 94%, transparent);
    padding: 0.58rem 0.72rem;
    color: var(--sw-text-primary);
    font-family: var(--sw-font-body);
    font-size: 0.79rem;
    font-weight: 500;
    line-height: 1.35;
    white-space: normal;
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
    --home-accent-color: var(--sw-accent-violet);
}

.home-tone--green {
    --home-accent-color: var(--sw-accent-green);
}

.home-tone--sun {
    --home-accent-color: var(--sw-accent-sun);
}

.home-accent-chip--violet .home-accent-chip__tooltip,
.home-accent-chip--green .home-accent-chip__tooltip,
.home-accent-chip--sun .home-accent-chip__tooltip {
    border-color: color-mix(
        in srgb,
        var(--home-accent-color) 28%,
        var(--sw-border)
    );
    background: color-mix(
        in srgb,
        var(--sw-bg-elevated) 88%,
        var(--home-accent-color) 12%
    );
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
    min-width: 0;
    color: var(--sw-text-primary);
    overflow-wrap: anywhere;
}

.home-hero__panel-title {
    font-family: var(--sw-font-body);
    font-size: clamp(1rem, 1.25vw, 1.12rem);
    font-weight: 600;
    line-height: 1.35;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 82%,
        var(--sw-text-secondary)
    );
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
    min-width: 0;
}

.home-section__footer-cta {
    display: flex;
    justify-content: flex-start;
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
    gap: var(--sw-space-xs);
    min-width: 0;
    border-color: color-mix(in srgb, var(--sw-border) 80%, transparent);
    background: var(--sw-bg-surface);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
    border-radius: inherit;
}

.home-card__body {
    display: grid;
    gap: 0.7rem;
    align-content: start;
    min-width: 0;
}

.home-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.home-card__tag {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
}

.home-card__summary {
    line-height: 1.45;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
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
    min-width: 0;
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
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 76%,
        var(--sw-text-secondary)
    );
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
    min-width: 0;
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
}

.home-card-link:active .home-card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .home-card-link:hover .home-card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    }
}

@media (min-width: 960px) {
    .home-hero {
        grid-template-columns: minmax(0, 1.45fr) minmax(18rem, 0.85fr);
    }

    .home-card {
        grid-template-columns: minmax(4.8rem, 6rem) minmax(0, 1fr);
        align-items: start;
        gap: 0.8rem;
    }

    .home-card :deep(.content-visual) {
        min-height: 100%;
        height: 100%;
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
        left: 50%;
        width: min(18rem, calc(100vw - 2rem));
        min-width: 0;
        max-width: calc(100vw - 2rem);
        transform: translate(-50%, 4px);
    }

    .home-accent-chip__tooltip::after {
        left: calc(50% - 5px);
    }

    .home-accent-chip:hover .home-accent-chip__tooltip,
    .home-accent-chip:focus-within .home-accent-chip__tooltip,
    .home-accent-chip:focus .home-accent-chip__tooltip {
        transform: translate(-50%, 0);
    }
}
</style>
