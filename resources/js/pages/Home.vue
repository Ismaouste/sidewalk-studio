<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import MetricStrip from '@/components/design-system/MetricStrip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, LabItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    highlights: string[];
    featuredCaseStudies: ContentItem[];
    latestWriting: ContentItem[];
    labs: LabItem[];
}>();

const highlightTones = ['dominant', 'green', 'coral'] as const;
const metricItems = computed(() => [
    {
        value: '04',
        label: 'Foundation tracks',
        note: 'Bootstrap, content, consent, SEO',
    },
    {
        value: props.featuredCaseStudies.length.toString().padStart(2, '0'),
        label: 'Featured cases',
        note: 'Public proof slices in the current release',
    },
    {
        value: props.latestWriting.length.toString().padStart(2, '0'),
        label: 'Fresh notes',
        note: 'Writing entries surfaced on the home page',
    },
    {
        value: props.labs.length.toString().padStart(2, '0'),
        label: 'Lab surfaces',
        note: 'Sandbox spaces for consent and structured data',
    },
    {
        value: 'v0',
        label: 'Release state',
        note: 'Local-first foundation, public shell in motion',
    },
]);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section sw-section--hero">
            <div class="home-hero">
                <SectionIntro
                    :eyebrow="props.hero.eyebrow"
                    :title="props.hero.title"
                    :description="props.hero.summary"
                    size="hero"
                >
                    <template #actions>
                        <Button href="/case-studies"
                            >Explore case studies</Button
                        >
                        <Button href="/writing" variant="secondary">
                            Read the writing log
                        </Button>
                    </template>

                    <LegendChip
                        label="Morning Grid / Sunset Signal"
                        tone="sun"
                    />
                    <LegendChip
                        label="Local-first Laravel shell"
                        tone="green"
                    />
                </SectionIntro>

                <Panel class="home-hero__panel" tone="grid">
                    <p class="type-eyebrow">Current frame</p>
                    <h2 class="type-h2 home-hero__panel-title">
                        The public release is proving the system, not only
                        decorating it.
                    </h2>
                    <ul class="home-hero__highlights">
                        <li
                            v-for="(highlight, index) in props.highlights"
                            :key="highlight"
                            class="home-hero__highlight"
                        >
                            <LegendChip
                                :label="`Frame 0${index + 1}`"
                                :tone="highlightTones[index] ?? 'violet'"
                            />
                            <p class="type-body-sm home-hero__highlight-copy">
                                {{ highlight }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>
        </section>

        <section class="sw-section sw-section--dense">
            <MetricStrip :items="metricItems" />
        </section>

        <SectionDivider label="Selected proof" />

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    eyebrow="Case studies"
                    title="Recent build decisions"
                    description="Small, public proof slices that show how the system is being shaped release by release."
                />
                <Button href="/case-studies" variant="ghost">View all</Button>
            </div>

            <div class="home-card-grid">
                <Link
                    v-for="item in props.featuredCaseStudies"
                    :key="item.slug"
                    :href="item.url"
                    class="home-card-link"
                >
                    <Panel class="home-card" tone="surface">
                        <LegendChip
                            :label="item.client || 'Internal build'"
                            tone="green"
                        />
                        <h3 class="type-h2 home-card__title">
                            {{ item.title }}
                        </h3>
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

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    eyebrow="Writing"
                    title="Notes from the system build"
                    description="Working notes that explain the architectural tradeoffs behind the portfolio itself."
                />
                <Button href="/writing" variant="ghost">Open archive</Button>
            </div>

            <div class="home-writing-list">
                <Link
                    v-for="item in props.latestWriting"
                    :key="item.slug"
                    :href="item.url"
                    class="home-writing-link"
                >
                    <Panel class="home-writing-card" tone="surface">
                        <div class="home-writing-card__meta">
                            <LegendChip label="Writing" tone="violet" />
                            <span class="type-meta">
                                {{ item.published_at }} ·
                                {{ item.reading_time }} min
                            </span>
                        </div>
                        <h3 class="type-h2 home-writing-card__title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body home-writing-card__summary">
                            {{ item.summary }}
                        </p>
                    </Panel>
                </Link>
            </div>
        </section>

        <section class="sw-section home-section">
            <div class="home-section__header">
                <SectionIntro
                    eyebrow="Labs"
                    title="Sandbox areas kept close to the product"
                    description="Focused technical proving grounds where consent, structure, and future interface patterns can be exercised safely."
                />
            </div>

            <div class="home-labs-grid">
                <Panel
                    v-for="lab in props.labs"
                    :key="lab.slug"
                    class="home-lab-card"
                    tone="grid"
                >
                    <div class="home-lab-card__header">
                        <h3 class="type-h3">{{ lab.title }}</h3>
                        <LegendChip :label="lab.status" tone="sun" />
                    </div>
                    <p class="type-body home-lab-card__summary">
                        {{ lab.summary }}
                    </p>
                    <div class="home-lab-card__stack">
                        <span
                            v-for="tech in lab.stack"
                            :key="tech"
                            class="type-meta home-lab-card__tech"
                        >
                            {{ tech }}
                        </span>
                    </div>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.home-hero {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
}

.home-hero__panel {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.home-hero__panel-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.home-hero__highlights {
    display: grid;
    gap: var(--sw-space-xs);
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

.home-hero__highlight-copy {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-section {
    display: grid;
    gap: var(--sw-space-sm);
}

.home-section__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
    align-items: end;
    justify-content: space-between;
}

.home-card-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.home-card-link,
.home-writing-link {
    display: block;
}

.home-card,
.home-writing-card,
.home-lab-card {
    display: grid;
    gap: var(--sw-space-xs);
    height: 100%;
    padding: var(--sw-space-sm);
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.home-card-link:hover .home-card,
.home-writing-link:hover .home-writing-card {
    transform: translateY(-2px);
    border-color: var(--sw-accent-dominant);
    box-shadow: var(--sw-shadow-md);
}

.home-card__title,
.home-writing-card__title {
    margin: 0;
    color: var(--sw-text-primary);
}

.home-card__summary,
.home-writing-card__summary,
.home-lab-card__summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.home-card__meta,
.home-lab-card__stack {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.home-card__tag,
.home-lab-card__tech {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 76%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.home-writing-list {
    display: grid;
    gap: var(--sw-space-sm);
}

.home-writing-card__meta,
.home-lab-card__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
    justify-content: space-between;
}

.home-labs-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

@media (min-width: 960px) {
    .home-hero {
        grid-template-columns: minmax(0, 1.45fr) minmax(18rem, 0.85fr);
    }
}

@media (max-width: 960px) {
    .home-card-grid,
    .home-labs-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
