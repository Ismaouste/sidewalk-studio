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

function caseStudyMeta(item: ContentItem) {
    return [
        {
            label: 'Published',
            value: item.published_at,
        },
        {
            label: 'Stack',
            value: `${item.stack.length} tools`,
        },
    ];
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section case-studies-index">
            <SectionIntro
                eyebrow="Case studies"
                title="Detailed decisions behind the first Sidewalk Studio release."
                description="Structured walkthroughs of repository bootstrap, consent orchestration, and SEO architecture choices."
            >
                <template #actions>
                    <Button href="/projects">View project tracks</Button>
                    <Button href="/contact" variant="secondary">
                        Discuss a similar build
                    </Button>
                </template>

                <LegendChip label="Technical review format" tone="green" />
                <LegendChip
                    :label="`${props.items.length} public slices`"
                    tone="sun"
                />
            </SectionIntro>

            <SectionDivider label="Case archive" />

            <div class="case-studies-index__grid">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    class="case-studies-index__link"
                >
                    <Panel class="case-studies-index__card" tone="grid">
                        <div class="case-studies-index__card-top">
                            <LegendChip
                                :label="item.client || 'Internal build'"
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
    position: relative;
    display: grid;
    gap: var(--sw-space-xs);
    height: 100%;
    overflow: hidden;
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.case-studies-index__card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 3px;
    transform: scaleX(0.24);
    transform-origin: left center;
    opacity: 0;
    background: linear-gradient(
        90deg,
        var(--sw-accent-green),
        color-mix(
            in srgb,
            var(--sw-accent-dominant) 58%,
            var(--sw-accent-green)
        )
    );
    transition:
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
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
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.case-studies-index__link:focus-visible {
    outline: none;
}

.case-studies-index__link:focus-visible .case-studies-index__card,
.case-studies-index__link:active .case-studies-index__card {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.case-studies-index__link:focus-visible .case-studies-index__card::before,
.case-studies-index__link:active .case-studies-index__card::before {
    transform: scaleX(1);
    opacity: 1;
}

.case-studies-index__link:active .case-studies-index__card {
    transform: translateY(1px);
}

@media (hover: hover) {
    .case-studies-index__link:hover .case-studies-index__card {
        transform: translateY(-2px);
        border-color: var(--sw-accent-dominant);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
        box-shadow: var(--sw-shadow-md);
    }

    .case-studies-index__link:hover .case-studies-index__card::before {
        transform: scaleX(1);
        opacity: 1;
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
