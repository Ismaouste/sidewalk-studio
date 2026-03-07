<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    tracksSection: {
        label: string;
        intro: {
            eyebrow: string;
            title: string;
            summary: string;
        };
        items: { title: string; summary: string }[];
    };
    caseStudies: ContentItem[];
    caseStudiesSection: {
        label: string;
        eyebrow: string;
        title: string;
        summary: string;
        archive_cta: string;
    };
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              experienceCta: 'Lire le parcours',
              localCta: 'Contexte local',
              trackPrefix: 'Piste',
              internalBuildLabel: 'Build interne',
          }
        : {
              experienceCta: 'Read experience',
              localCta: 'Local context',
              trackPrefix: 'Track',
              internalBuildLabel: 'Internal build',
          },
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section projects-page">
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <template #actions>
                    <Button href="/experience" variant="secondary">
                        {{ copy.experienceCta }}
                    </Button>
                    <Button href="/local" variant="ghost">
                        {{ copy.localCta }}
                    </Button>
                </template>
            </SectionIntro>

            <div class="projects-page__tracks">
                <Panel
                    v-for="(track, index) in props.tracksSection.items"
                    :key="track.title"
                    class="projects-page__track"
                    tone="surface"
                >
                    <LegendChip
                        :label="`${copy.trackPrefix} 0${index + 1}`"
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

            <SectionDivider :label="props.caseStudiesSection.label" />

            <div class="projects-page__header">
                <SectionIntro
                    :eyebrow="props.caseStudiesSection.eyebrow"
                    :title="props.caseStudiesSection.title"
                    :description="props.caseStudiesSection.summary"
                />
                <Button href="/case-studies" variant="ghost">
                    {{ props.caseStudiesSection.archive_cta }}
                </Button>
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
                            :label="item.client || copy.internalBuildLabel"
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

.projects-page__header :deep(.section-intro) {
    max-width: 46rem;
}

.projects-page__cases {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.projects-page__case-link {
    display: block;
    border-radius: var(--sw-radius-lg);
}

.projects-page__case {
    position: relative;
    height: 100%;
    overflow: hidden;
    transition:
        transform var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.projects-page__case::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 3px;
    transform: scaleX(0.24);
    transform-origin: left center;
    opacity: 0;
    background: linear-gradient(
        90deg,
        var(--sw-accent-dominant),
        color-mix(in srgb, var(--sw-accent-sun) 68%, var(--sw-accent-dominant))
    );
    transition:
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
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

.projects-page__case-link:focus-visible {
    outline: none;
}

.projects-page__case-link:focus-visible .projects-page__case,
.projects-page__case-link:active .projects-page__case {
    border-color: var(--sw-border-focus);
    box-shadow: var(--sw-shadow-md);
}

.projects-page__case-link:focus-visible .projects-page__case::before,
.projects-page__case-link:active .projects-page__case::before {
    transform: scaleX(1);
    opacity: 1;
}

.projects-page__case-link:active .projects-page__case {
    transform: translateY(1px);
}

@media (hover: hover) {
    .projects-page__case-link:hover .projects-page__case {
        transform: translateY(-2px);
        border-color: var(--sw-accent-dominant);
        background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
        box-shadow: var(--sw-shadow-md);
    }

    .projects-page__case-link:hover .projects-page__case::before {
        transform: scaleX(1);
        opacity: 1;
    }
}

@media (max-width: 960px) {
    .projects-page__tracks,
    .projects-page__cases {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .projects-page {
        gap: var(--sw-space-xs);
    }

    .projects-page__header {
        gap: var(--sw-space-xs);
        align-items: start;
    }
}
</style>
