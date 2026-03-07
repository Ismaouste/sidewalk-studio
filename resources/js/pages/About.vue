<script setup lang="ts">
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    principles: string[];
    timeline: string[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section about-page">
            <SectionIntro
                eyebrow="About the build"
                title="A portfolio designed as a calm engineering object."
                description="Sidewalk Studio exists to make technical judgment visible: not only the final UI, but also the quality of the system underneath it."
            />

            <SectionDivider label="Practice" />

            <div class="about-page__grid">
                <Panel class="about-page__panel" tone="surface">
                    <p class="type-eyebrow">Principles</p>
                    <div class="about-page__list">
                        <article
                            v-for="principle in props.principles"
                            :key="principle"
                            class="about-page__item"
                        >
                            <LegendChip label="Principle" tone="green" />
                            <p class="type-body about-page__copy">
                                {{ principle }}
                            </p>
                        </article>
                    </div>
                </Panel>

                <Panel class="about-page__panel" tone="grid">
                    <p class="type-eyebrow">Trajectory</p>
                    <ol class="about-page__list">
                        <li
                            v-for="(step, index) in props.timeline"
                            :key="step"
                            class="about-page__item"
                        >
                            <LegendChip
                                :label="`Step 0${index + 1}`"
                                tone="coral"
                            />
                            <p class="type-body about-page__copy">
                                {{ step }}
                            </p>
                        </li>
                    </ol>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.about-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.about-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: minmax(0, 1.15fr) minmax(18rem, 0.85fr);
}

.about-page__panel {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.about-page__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.about-page__item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.about-page__item:first-child {
    border-top: 0;
    padding-top: 0;
}

.about-page__copy {
    margin: 0;
    color: var(--sw-text-secondary);
}

@media (max-width: 960px) {
    .about-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
