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

const writingMeta = computed(() => [
    {
        label: copy.value.publishedLabel,
        value: props.item.published_at,
    },
    {
        label: copy.value.updatedLabel,
        value: props.item.updated_at,
    },
    {
        label: copy.value.readLabel,
        value: `${props.item.reading_time} min`,
    },
]);

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              backCta: 'Retour aux notes',
              eyebrow: 'Note',
              editorialLabel: 'Note editoriale',
              taggedThreadsLabel: `${props.item.tags.length} fils etiquetes`,
              dividerLabel: 'Entree',
              entryFrameLabel: "Cadre d'entree",
              continueLabel: 'Poursuivre le fil',
              continueDescription:
                  "Les cas clients montrent comment les memes choix d'architecture tiennent quand on ajoute pression de livraison, contraintes parties prenantes et discipline produit.",
              caseStudiesCta: 'Ouvrir cas clients',
              projectsCta: 'Projets',
              experienceCta: 'Parcours',
              contactCta: 'Contact',
              publishedLabel: 'Publie',
              updatedLabel: 'Maj',
              readLabel: 'Lecture',
          }
        : {
              backCta: 'Back to writing',
              eyebrow: 'Writing entry',
              editorialLabel: 'Editorial note',
              taggedThreadsLabel: `${props.item.tags.length} tagged threads`,
              dividerLabel: 'Entry',
              entryFrameLabel: 'Entry frame',
              continueLabel: 'Continue the thread',
              continueDescription:
                  'Case studies show how the same architectural choices hold up once delivery pressure, stakeholder constraints, and product discipline enter the picture.',
              caseStudiesCta: 'Open case studies',
              projectsCta: 'Projects',
              experienceCta: 'Experience',
              contactCta: 'Contact',
              publishedLabel: 'Published',
              updatedLabel: 'Updated',
              readLabel: 'Read',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section writing-show">
            <div class="writing-show__lead">
                <Button href="/writing" variant="ghost" size="sm">
                    {{ copy.backCta }}
                </Button>

                <SectionIntro
                    :eyebrow="copy.eyebrow"
                    :title="props.item.title"
                    :description="props.item.excerpt || props.item.summary"
                >
                    <LegendChip :label="copy.editorialLabel" tone="violet" />
                    <LegendChip :label="copy.taggedThreadsLabel" tone="sun" />
                </SectionIntro>

                <ContentMetaRow :items="writingMeta" />
            </div>

            <SectionDivider :label="copy.dividerLabel" />

            <div class="writing-show__layout">
                <Panel
                    as="article"
                    class="writing-show__article"
                    tone="elevated"
                >
                    <RichText :html="props.item.body_html" />
                </Panel>

                <aside class="writing-show__aside">
                    <Panel class="writing-show__sidebar-card" tone="surface">
                        <p class="type-eyebrow">{{ copy.entryFrameLabel }}</p>
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
                            <Button
                                href="/projects"
                                variant="secondary"
                                size="sm"
                            >
                                {{ copy.projectsCta }}
                            </Button>
                            <Button
                                href="/experience"
                                variant="secondary"
                                size="sm"
                            >
                                {{ copy.experienceCta }}
                            </Button>
                            <Button href="/contact" variant="ghost" size="sm">
                                {{ copy.contactCta }}
                            </Button>
                        </div>
                    </Panel>
                </aside>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.writing-show {
    display: grid;
    gap: var(--sw-space-sm);
}

.writing-show__lead {
    display: grid;
    gap: var(--sw-space-xs);
    max-width: 58rem;
}

.writing-show__layout {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
}

.writing-show__article {
    padding: clamp(1.5rem, 3vw, 2.5rem);
}

.writing-show__aside {
    display: grid;
    gap: var(--sw-space-sm);
}

.writing-show__sidebar-card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.writing-show__sidebar-copy {
    margin: 0;
    color: var(--sw-text-secondary);
}

.writing-show__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.writing-show__tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.writing-show__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

@media (min-width: 1040px) {
    .writing-show__layout {
        grid-template-columns: minmax(0, 1fr) minmax(18rem, 20rem);
    }
}
</style>
