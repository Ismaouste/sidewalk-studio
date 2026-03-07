<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ContentMetaRow from '@/components/design-system/ContentMetaRow.vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();

function writingMeta(item: ContentItem) {
    return [
        {
            label: 'Published',
            value: item.published_at,
        },
        {
            label: 'Read',
            value: `${item.reading_time} min`,
        },
    ];
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section writing-index">
            <SectionIntro
                eyebrow="Writing archive"
                title="Build notes, strategy memos, and architecture rationale."
                description="Editorial notes on content systems, consent orchestration, SSR readiness, and public repo discipline."
            >
                <template #actions>
                    <Button href="/projects">View projects</Button>
                    <Button href="/experience" variant="secondary">
                        Read experience
                    </Button>
                </template>

                <LegendChip label="Editorial log" tone="violet" />
                <LegendChip
                    :label="`${props.items.length} published entries`"
                    tone="sun"
                />
            </SectionIntro>

            <SectionDivider label="Published entries" />

            <div class="writing-index__list">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    class="writing-index__link"
                >
                    <Panel class="writing-index__card" tone="surface">
                        <div class="writing-index__card-top">
                            <LegendChip label="Writing" tone="violet" />
                            <ContentMetaRow :items="writingMeta(item)" />
                        </div>

                        <h2 class="type-h2 writing-index__title">
                            {{ item.title }}
                        </h2>

                        <p class="type-body writing-index__summary">
                            {{ item.summary }}
                        </p>

                        <div class="writing-index__tags">
                            <span
                                v-for="tag in item.tags"
                                :key="tag"
                                class="type-meta writing-index__tag"
                            >
                                {{ tag }}
                            </span>
                        </div>
                    </Panel>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.writing-index {
    display: grid;
    gap: var(--sw-space-sm);
}

.writing-index__list {
    display: grid;
    gap: var(--sw-space-sm);
    max-width: 60rem;
}

.writing-index__link {
    display: block;
}

.writing-index__card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.writing-index__link:hover .writing-index__card {
    transform: translateY(-2px);
    border-color: var(--sw-accent-violet);
    box-shadow: var(--sw-shadow-md);
}

.writing-index__card-top {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    align-items: center;
    justify-content: space-between;
}

.writing-index__title,
.writing-index__summary {
    margin: 0;
}

.writing-index__title {
    color: var(--sw-text-primary);
}

.writing-index__summary {
    max-width: 48rem;
    color: var(--sw-text-secondary);
}

.writing-index__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.writing-index__tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}
</style>
