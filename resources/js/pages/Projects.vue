<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
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
    caseStudies: ContentItem[];
    tracks: { title: string; summary: string }[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section projects-page">
            <SectionIntro
                eyebrow="Project map"
                title="Three tracks define the v0."
                description="Repository discipline, content structure, and privacy-aware public experience are the current proving grounds."
            >
                <template #actions>
                    <Button href="/experience" variant="secondary">
                        Read experience
                    </Button>
                    <Button href="/local" variant="ghost">
                        Local context
                    </Button>
                </template>
            </SectionIntro>

            <div class="projects-page__tracks">
                <Panel
                    v-for="(track, index) in props.tracks"
                    :key="track.title"
                    class="projects-page__track"
                    tone="surface"
                >
                    <LegendChip
                        :label="`Track 0${index + 1}`"
                        :tone="
                            index === 0
                                ? 'dominant'
                                : index === 1
                                  ? 'green'
                                  : 'coral'
                        "
                    />
                    <h3 class="type-h2 projects-page__track-title">
                        {{ track.title }}
                    </h3>
                    <p class="type-body projects-page__track-summary">
                        {{ track.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider label="Selected case studies" />

            <div class="projects-page__header">
                <SectionIntro
                    eyebrow="Case studies"
                    title="A small set of public implementation slices."
                    description="Each one documents a real decision path rather than a polished end-state alone."
                />
                <Button href="/case-studies" variant="ghost"
                    >View archive</Button
                >
            </div>

            <div class="projects-page__cases">
                <Link
                    v-for="item in props.caseStudies"
                    :key="item.slug"
                    :href="item.url"
                    class="projects-page__case-link"
                >
                    <Panel class="projects-page__case" tone="grid">
                        <LegendChip
                            :label="item.client || 'Internal build'"
                            tone="green"
                        />
                        <h3 class="type-h2 projects-page__case-title">
                            {{ item.title }}
                        </h3>
                        <p class="type-body projects-page__case-summary">
                            {{ item.summary }}
                        </p>
                        <div class="projects-page__case-tags">
                            <span
                                v-for="tag in item.tags"
                                :key="tag"
                                class="type-meta projects-page__case-tag"
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
.projects-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.projects-page__tracks {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.projects-page__track,
.projects-page__case {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.projects-page__track-title,
.projects-page__case-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.projects-page__track-summary,
.projects-page__case-summary {
    margin: 0;
    color: var(--sw-text-secondary);
}

.projects-page__header {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-sm);
    align-items: end;
    justify-content: space-between;
}

.projects-page__cases {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.projects-page__case-link {
    display: block;
}

.projects-page__case {
    height: 100%;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.projects-page__case-link:hover .projects-page__case {
    transform: translateY(-2px);
    border-color: var(--sw-accent-dominant);
    box-shadow: var(--sw-shadow-md);
}

.projects-page__case-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.projects-page__case-tag {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

@media (max-width: 960px) {
    .projects-page__tracks,
    .projects-page__cases {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
