<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import RichText from '@/components/RichText.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    item: ContentItem;
}>();

const caseStudyMeta = computed(() => [
    {
        label: copy.value.publishedLabel,
        value: props.item.published_at,
    },
    {
        label: copy.value.updatedLabel,
        value: props.item.updated_at,
    },
    {
        label: copy.value.outcomesLabel,
        value: `${props.item.outcomes.length} ${copy.value.signalsSuffix}`,
    },
]);

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              backCta: 'Retour aux cas clients',
              eyebrow: 'Cas client',
              internalBuildLabel: 'Build interne',
              implementationToolsLabel: `${props.item.stack.length} outils implémentation`,
              dividerLabel: "Journal d'implémentation",
              projectFrameLabel: 'Cadre projet',
              clientLabel: 'Client',
              roleLabel: 'Rôle',
              stackLabel: 'Stack',
              contactCta: 'Discuter un brief similaire',
              outcomesTitle: 'Résultats',
              publishedLabel: 'Publié',
              updatedLabel: 'Maj',
              outcomesLabel: 'Résultats',
              signalsSuffix: 'signaux',
          }
        : {
              backCta: 'Back to case studies',
              eyebrow: 'Case study',
              internalBuildLabel: 'Internal build',
              implementationToolsLabel: `${props.item.stack.length} implementation tools`,
              dividerLabel: 'Implementation log',
              projectFrameLabel: 'Project frame',
              clientLabel: 'Client',
              roleLabel: 'Role',
              stackLabel: 'Stack',
              contactCta: 'Discuss a similar brief',
              outcomesTitle: 'Outcomes',
              publishedLabel: 'Published',
              updatedLabel: 'Updated',
              outcomesLabel: 'Outcomes',
              signalsSuffix: 'signals',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section case-study-show">
            <div class="case-study-show__lead">
                <Button href="/case-studies" variant="ghost" size="sm">
                    {{ copy.backCta }}
                </Button>

                <SectionIntro
                    :eyebrow="copy.eyebrow"
                    :title="props.item.title"
                    :description="props.item.summary"
                >
                    <LegendChip
                        :label="props.item.client || copy.internalBuildLabel"
                        tone="green"
                    />
                    <LegendChip
                        :label="copy.implementationToolsLabel"
                        tone="dominant"
                    />
                </SectionIntro>

                <ContentMetaRow :items="caseStudyMeta" />
            </div>

            <SectionDivider :label="copy.dividerLabel" />

            <div class="case-study-show__layout">
                <Panel
                    as="article"
                    class="case-study-show__article"
                    tone="elevated"
                >
                    <RichText :html="props.item.body_html" />
                </Panel>

                <aside class="case-study-show__aside">
                    <Panel class="case-study-show__sidebar-card" tone="grid">
                        <p class="type-eyebrow">{{ copy.projectFrameLabel }}</p>

                        <dl class="case-study-show__details">
                            <div class="case-study-show__detail">
                                <dt class="type-nav">{{ copy.clientLabel }}</dt>
                                <dd class="type-body-sm">
                                    {{
                                        props.item.client ||
                                        copy.internalBuildLabel
                                    }}
                                </dd>
                            </div>

                            <div class="case-study-show__detail">
                                <dt class="type-nav">{{ copy.roleLabel }}</dt>
                                <dd class="type-body-sm">
                                    {{ props.item.role }}
                                </dd>
                            </div>

                            <div class="case-study-show__detail">
                                <dt class="type-nav">{{ copy.stackLabel }}</dt>
                                <dd class="case-study-show__stack">
                                    <span
                                        v-for="tech in props.item.stack"
                                        :key="tech"
                                        class="type-meta case-study-show__stack-item"
                                    >
                                        {{ tech }}
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <div class="case-study-show__actions">
                            <Button
                                href="/contact"
                                variant="secondary"
                                size="sm"
                            >
                                {{ copy.contactCta }}
                            </Button>
                        </div>
                    </Panel>

                    <Panel class="case-study-show__sidebar-card" tone="surface">
                        <p class="type-eyebrow">{{ copy.outcomesTitle }}</p>

                        <ul class="case-study-show__outcomes">
                            <li
                                v-for="outcome in props.item.outcomes"
                                :key="outcome"
                                class="type-body-sm case-study-show__outcome"
                            >
                                {{ outcome }}
                            </li>
                        </ul>
                    </Panel>
                </aside>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.case-study-show {
    display: grid;
    gap: var(--sw-space-sm);
}

.case-study-show__lead {
    display: grid;
    gap: var(--sw-space-xs);
    max-width: 60rem;
}

.case-study-show__layout {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
}

.case-study-show__article {
    padding: clamp(1.5rem, 3vw, 2.5rem);
}

.case-study-show__aside {
    display: grid;
    gap: var(--sw-space-sm);
}

.case-study-show__sidebar-card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.case-study-show__details {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
}

.case-study-show__detail {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.case-study-show__detail:first-child {
    border-top: 0;
    padding-top: 0;
}

.case-study-show__detail dd {
    margin: 0;
    color: var(--sw-text-secondary);
}

.case-study-show__stack {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.case-study-show__stack-item {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.case-study-show__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

.case-study-show__outcomes {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.case-study-show__outcome {
    margin: 0;
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
    color: var(--sw-text-secondary);
}

.case-study-show__outcome:first-child {
    border-top: 0;
    padding-top: 0;
}

@media (min-width: 1040px) {
    .case-study-show__layout {
        grid-template-columns: minmax(0, 1fr) minmax(19rem, 21rem);
    }
}
</style>
