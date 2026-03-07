<script setup lang="ts">
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
import type { ContentItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    item: ContentItem;
}>();

const writingMeta = computed(() => [
    {
        label: 'Published',
        value: props.item.published_at,
    },
    {
        label: 'Updated',
        value: props.item.updated_at,
    },
    {
        label: 'Read',
        value: `${props.item.reading_time} min`,
    },
]);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section writing-show">
            <div class="writing-show__lead">
                <Button href="/writing" variant="ghost" size="sm">
                    Back to writing
                </Button>

                <SectionIntro
                    eyebrow="Writing entry"
                    :title="props.item.title"
                    :description="props.item.excerpt || props.item.summary"
                >
                    <LegendChip label="Editorial note" tone="violet" />
                    <LegendChip
                        :label="`${props.item.tags.length} tagged threads`"
                        tone="sun"
                    />
                </SectionIntro>

                <ContentMetaRow :items="writingMeta" />
            </div>

            <SectionDivider label="Entry" />

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
                        <p class="type-eyebrow">Entry frame</p>
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
                        <p class="type-eyebrow">Continue the thread</p>
                        <p class="type-body-sm writing-show__sidebar-copy">
                            Case studies show how the same architectural choices
                            behave under delivery pressure and stakeholder
                            constraints.
                        </p>

                        <div class="writing-show__actions">
                            <Button href="/case-studies" size="sm">
                                Open case studies
                            </Button>
                            <Button
                                href="/contact"
                                variant="secondary"
                                size="sm"
                            >
                                Contact
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
