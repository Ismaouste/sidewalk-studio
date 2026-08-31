<script setup lang="ts">
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import MediaEmbed from '@/components/MediaEmbed.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { LabItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    labs: LabItem[];
    embedDemo: {
        title: string;
        description: string;
        service: string;
        id: string;
    };
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section labs-page">
            <div class="labs-page__hero">
                <SectionIntro
                    eyebrow="Labs"
                    title="Sandbox the risky parts before they reach production code."
                    description="Focused proving grounds for consent, structured data, and future interface decisions that still need real-world pressure."
                >
                    <template #actions>
                        <Button href="/case-studies">See shipped slices</Button>
                        <Button href="/contact" variant="secondary">
                            Discuss an audit
                        </Button>
                    </template>

                    <LegendChip
                        :label="`${props.labs.length} active surfaces`"
                        tone="sun"
                    />
                    <LegendChip
                        label="Exploratory, not detached"
                        tone="violet"
                    />
                </SectionIntro>

                <Panel class="labs-page__demo" tone="grid">
                    <p class="type-eyebrow">Consent demo</p>
                    <h2 class="type-h2 labs-page__demo-title">
                        {{ props.embedDemo.title }}
                    </h2>
                    <p class="type-body labs-page__demo-copy">
                        {{ props.embedDemo.description }}
                    </p>

                    <MediaEmbed
                        :id="props.embedDemo.id"
                        :title="props.embedDemo.title"
                        :service="props.embedDemo.service"
                    />
                </Panel>
            </div>

            <div class="labs-page__grid">
                <Panel
                    v-for="(lab, index) in props.labs"
                    :key="lab.slug"
                    class="labs-page__card"
                    tone="surface"
                >
                    <div class="labs-page__card-top">
                        <LegendChip :label="lab.status" tone="sun" />
                        <span class="type-meta">Lab 0{{ index + 1 }}</span>
                    </div>

                    <h3 class="type-h2 labs-page__card-title">
                        {{ lab.title }}
                    </h3>

                    <p class="type-body labs-page__card-summary">
                        {{ lab.summary }}
                    </p>

                    <div class="labs-page__stack">
                        <span
                            v-for="item in lab.stack"
                            :key="item"
                            class="type-meta labs-page__stack-item"
                        >
                            {{ item }}
                        </span>
                    </div>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.labs-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.labs-page__hero {
    display: grid;
    gap: var(--sw-space-sm);
    align-items: start;
}

.labs-page__demo,
.labs-page__card {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.labs-page__demo-title,
.labs-page__card-title,
.labs-page__card-summary,
.labs-page__demo-copy {
    margin: 0;
}

.labs-page__demo-copy,
.labs-page__card-summary {
    color: var(--sw-text-secondary);
}

.labs-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.labs-page__card-top {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
    justify-content: space-between;
}

.labs-page__stack {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.labs-page__stack-item {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

@media (min-width: 1040px) {
    .labs-page__hero {
        grid-template-columns: minmax(0, 1.15fr) minmax(20rem, 0.85fr);
    }
}

@media (max-width: 960px) {
    .labs-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
