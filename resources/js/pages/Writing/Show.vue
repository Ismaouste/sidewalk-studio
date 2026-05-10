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
              eyebrow: props.item.category === 'journal' ? 'Journal' : 'Note',
              editorialLabel:
                  props.item.category === 'journal'
                      ? 'Article'
                      : 'Note éditoriale',
              taggedThreadsLabel: `${props.item.tags.length} fils étiquetés`,
              continueLabel: 'Poursuivre le fil',
              continueDescription:
                  "Les cas clients montrent comment les mêmes choix d'architecture se comportent sous pression de livraison et contraintes parties prenantes.",
              caseStudiesCta: 'Ouvrir cas clients',
              contactCta: 'Contact',
              publishedLabel: 'Publié',
              updatedLabel: 'Maj',
              readLabel: 'Lecture',
              relatedEyebrow: 'À lire ensuite',
              relatedTitle: 'Autres notes et articles dans le même fil',
          }
        : {
              eyebrow: props.item.category === 'journal' ? 'Journal' : 'Note',
              editorialLabel:
                  props.item.category === 'journal'
                      ? 'Article'
                      : 'Editorial note',
              taggedThreadsLabel: `${props.item.tags.length} tagged threads`,
              continueLabel: 'Continue the thread',
              continueDescription:
                  'Case studies show how the same architectural choices behave under delivery pressure and stakeholder constraints.',
              caseStudiesCta: 'Open case studies',
              contactCta: 'Contact',
              publishedLabel: 'Published',
              updatedLabel: 'Updated',
              readLabel: 'Read',
              relatedEyebrow: 'Read next',
              relatedTitle: 'More notes and articles in the same thread',
          },
);

const writingMeta = computed(() => [
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
        label: copy.value.readLabel,
        value: `${props.item.reading_time} min`,
    },
]);
</script>

<template>
    <ArticleShowLayout :seo="props.seo">
        <template #lead>
            <SectionIntro
                :eyebrow="copy.eyebrow"
                :title="props.item.title"
                :description="props.item.excerpt || props.item.summary"
            >
                <LegendChip :label="copy.editorialLabel" tone="violet" />
                <LegendChip :label="copy.taggedThreadsLabel" tone="sun" />
            </SectionIntro>

            <ContentMetaRow :items="writingMeta" />
        </template>

        <template #article>
            <ContentVisual :item="props.item" />
            <RichText :html="props.item.body_html" />
        </template>

        <template #aside>
            <Panel class="writing-show__sidebar-card" tone="surface">
                <p class="type-eyebrow">{{ copy.editorialLabel }}</p>
                <p class="type-body-sm writing-show__sidebar-copy">
                    {{ props.item.summary }}
                </p>

                <div class="writing-show__tags">
                    <span
                        v-for="tag in props.item.tags"
                        :key="tag"
                        class="type-meta writing-show__tag"
                    >
                        {{ tag }}
                    </span>
                </div>
            </Panel>

            <Panel class="writing-show__sidebar-card" tone="grid">
                <p class="type-eyebrow">{{ copy.continueLabel }}</p>
                <p class="type-body-sm writing-show__sidebar-copy">
                    {{ copy.continueDescription }}
                </p>

                <div class="writing-show__actions">
                    <Button href="/case-studies" size="sm">
                        {{ copy.caseStudiesCta }}
                    </Button>
                    <Button href="/contact" variant="secondary" size="sm">
                        {{ copy.contactCta }}
                    </Button>
                </div>
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
.writing-show__sidebar-card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.writing-show__sidebar-copy {
    margin: 0;
    color: var(--sw-text-secondary);
    overflow-wrap: anywhere;
    word-break: break-word;
}

.writing-show__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.writing-show__tag {
    display: inline-flex;
    align-items: center;
    color: var(--sw-text-muted);
    overflow-wrap: anywhere;
    word-break: break-word;
}

.writing-show__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

@media (max-width: 640px) {
    .writing-show__sidebar-card {
        padding: var(--sw-space-xs);
    }
}
</style>
