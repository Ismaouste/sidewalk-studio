<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ContentVisual from '@/components/content/ContentVisual.vue';
import RelatedItemsStrip from '@/components/content/RelatedItemsStrip.vue';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import ArticleShowLayout from '@/components/layout/ArticleShowLayout.vue';
import RichText from '@/components/RichText.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { formatPublicDate } from '@/lib/formatDate';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    item: ContentItem;
    related?: ContentItem[];
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              eyebrow: 'Cas client',
              internalBuildLabel: 'Build interne',
              implementationToolsLabel: `${props.item.stack.length} outils implémentation`,
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
              relatedEyebrow: 'Cas suivants',
              relatedTitle: 'Autres cas clients à explorer',
          }
        : {
              eyebrow: 'Case study',
              internalBuildLabel: 'Internal build',
              implementationToolsLabel: `${props.item.stack.length} implementation tools`,
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
              relatedEyebrow: 'Next case studies',
              relatedTitle: 'Other case studies worth exploring',
          },
);

const caseStudyMeta = computed(() => [
    {
        label: copy.value.publishedLabel,
        value: formatPublicDate(
            props.item.published_at,
            page.props.site.locale,
            'long',
        ),
    },
    {
        label: copy.value.updatedLabel,
        value: formatPublicDate(
            props.item.updated_at,
            page.props.site.locale,
            'long',
        ),
    },
    {
        label: copy.value.outcomesLabel,
        value: `${props.item.outcomes.length} ${copy.value.signalsSuffix}`,
    },
]);
</script>

<template>
    <ArticleShowLayout :seo="props.seo">
        <template #lead>
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
        </template>

        <template #article>
            <ContentVisual :item="props.item" />
            <RichText :html="props.item.body_html" />
        </template>

        <template #aside>
            <Panel class="case-study-show__sidebar-card" tone="grid">
                <p class="type-eyebrow">{{ copy.projectFrameLabel }}</p>

                <dl class="case-study-show__details">
                    <div class="case-study-show__detail">
                        <dt class="type-nav">{{ copy.clientLabel }}</dt>
                        <dd class="type-body-sm">
                            {{ props.item.client || copy.internalBuildLabel }}
                        </dd>
                    </div>

                    <div class="case-study-show__detail">
                        <dt class="type-nav">{{ copy.roleLabel }}</dt>
                        <dd class="type-body-sm">{{ props.item.role }}</dd>
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
                    <Button href="/contact" variant="secondary" size="sm">
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
        </template>

        <template v-if="props.related && props.related.length > 0" #footer>
            <RelatedItemsStrip
                :items="props.related"
                :eyebrow="copy.relatedEyebrow"
                :title="copy.relatedTitle"
            />
        </template>
    </ArticleShowLayout>
</template>

<style scoped>
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
    overflow-wrap: anywhere;
    word-break: break-word;
}

.case-study-show__stack {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.case-study-show__stack-item {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
    overflow-wrap: anywhere;
    word-break: break-word;
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
    overflow-wrap: anywhere;
    word-break: break-word;
}

.case-study-show__outcome:first-child {
    border-top: 0;
    padding-top: 0;
}

@media (max-width: 640px) {
    .case-study-show__sidebar-card {
        padding: var(--sw-space-xs);
    }
}
</style>
