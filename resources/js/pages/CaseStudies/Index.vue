<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { formatPublicDate } from '@/lib/formatDate';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();

function caseStudyMeta(item: ContentItem) {
    return [
        {
            label: copy.value.publishedLabel,
            value: formatPublicDate(
                item.published_at,
                page.props.site.locale,
                'month',
            ),
        },
        {
            label: copy.value.stackLabel,
            value: `${item.stack.length} ${copy.value.toolsSuffix}`,
        },
    ];
}

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              eyebrow: 'Études de cas',
              title: 'Décisions techniques, outils utiles, détails qui comptent.',
              description:
                  "Des cas plus précis autour des flux produit, de l'auto-hébergement, du consentement, du SEO technique et des systèmes web qui demandent de la tenue.",
              projectsCta: 'Lire les expériences',
              contactCta: 'Discuter un build similaire',
              reviewLabel: 'Format revue technique',
              publicSlicesLabel: `${props.items.length} cas publiés`,
              internalBuildLabel: 'Build interne',
              publishedLabel: 'Publié',
              stackLabel: 'Stack',
              toolsSuffix: 'outils',
          }
        : {
              eyebrow: 'Case studies',
              title: 'Technical decisions, useful tools, details that matter.',
              description:
                  'More focused cases around product-data flows, self-hosting, consent, technical SEO, and web systems that need real staying power.',
              projectsCta: 'Read the experience',
              contactCta: 'Discuss a similar build',
              reviewLabel: 'Technical review format',
              publicSlicesLabel: `${props.items.length} published cases`,
              internalBuildLabel: 'Internal build',
              publishedLabel: 'Published',
              stackLabel: 'Stack',
              toolsSuffix: 'tools',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section case-studies-index">
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="copy.title"
                :description="copy.description"
            >
                <template #actions>
                    <Button href="/projects">{{ copy.projectsCta }}</Button>
                    <Button href="/contact" variant="secondary">
                        {{ copy.contactCta }}
                    </Button>
                </template>

                <LegendChip :label="copy.reviewLabel" tone="green" />
                <LegendChip :label="copy.publicSlicesLabel" tone="sun" />
            </SectionIntro>

            <div class="case-studies-index__grid">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    class="case-studies-index__link"
                >
                    <Panel class="case-studies-index__card" tone="grid">
                        <ContentVisual :item="item" compact />
                        <div class="case-studies-index__card-top">
                            <LegendChip
                                :label="item.client || copy.internalBuildLabel"
                                tone="green"
                            />
                            <ContentMetaRow :items="caseStudyMeta(item)" />
                        </div>

                        <h2 class="type-h2 case-studies-index__title">
                            {{ item.title }}
                        </h2>

                        <p class="type-body-sm case-studies-index__role">
                            {{ item.role }}
                        </p>

                        <p class="type-body case-studies-index__summary">
                            {{ item.summary }}
                        </p>

                        <div class="case-studies-index__stack">
                            <span
                                v-for="tech in item.stack"
                                :key="tech"
                                class="type-meta case-studies-index__stack-item"
                            >
                                {{ tech }}
                            </span>
                        </div>
                    </Panel>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.case-studies-index {
    display: grid;
    gap: var(--sw-space-sm);
}

.case-studies-index__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.case-studies-index__link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.case-studies-index__card {
    display: grid;
    gap: var(--sw-space-xs);
    height: 100%;
    padding: clamp(12px, 2vw, 16px);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.case-studies-index__card-top {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    align-items: center;
    justify-content: space-between;
}

.case-studies-index__title,
.case-studies-index__role,
.case-studies-index__summary {
    margin: 0;
}

.case-studies-index__title {
    color: var(--sw-text-primary);
}

.case-studies-index__role,
.case-studies-index__summary {
    color: var(--sw-text-secondary);
}

.case-studies-index__stack {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.case-studies-index__stack-item {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
}

.case-studies-index__link:focus-visible {
    outline: none;
}

.case-studies-index__link:focus-visible .case-studies-index__card,
.case-studies-index__link:active .case-studies-index__card {
    border-color: var(--sw-border-focus);
}

.case-studies-index__link:active .case-studies-index__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .case-studies-index__link:hover .case-studies-index__card {
        transform: translateY(-2px);
        border-color: var(--sw-card-hover-border);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    }
}

@media (max-width: 960px) {
    .case-studies-index__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .case-studies-index {
        gap: var(--sw-space-xs);
    }

    .case-studies-index__card-top {
        align-items: start;
        justify-content: flex-start;
    }
}
</style>
