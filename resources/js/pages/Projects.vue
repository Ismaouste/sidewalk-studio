<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DataPlate from '@/components/experience/DataPlate.vue';
import EditorialSpread from '@/components/experience/EditorialSpread.vue';
import ManifestoOpener from '@/components/experience/ManifestoOpener.vue';
import SignageStrip from '@/components/experience/SignageStrip.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

type ExperienceSection = {
    title: string;
    eyebrow: string;
    summary: string;
    paragraphs: string[];
    detail_groups: Array<{
        title: string;
        items: string[];
        pills?: string[];
    }>;
    marginalia?: { author: string; quote: string };
};

const page = usePage<{ site: SiteProps }>();

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    thesis: string;
    positioning: string[];
    contexts: string[];
    professionalSections: ExperienceSection[];
    associativeSections: ExperienceSection[];
    sideProjectSections: ExperienceSection[];
    trajectory: Array<{ title: string; summary: string }>;
    strengths: string[];
    focusAreas: Array<{ title: string; summary: string }>;
    lookingFor: string;
    cvDownloads: Array<{ label: string; href: string }>;
}>();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              overviewCta: 'Découvrir toutes les études de cas',
              contactCta: "Discuter d'un contexte proche",
              openerEyebrow: 'Comment je travaille',
              signageAriaLabel: 'Aller à un projet',
              spreadStackLabel: 'Stack',
              trajectoryLabel: 'Parcours',
              strengthsLabel: 'Forces',
              focusAreasLabel: 'Domaines',
              lookingForLabel: 'Ce que je recherche',
          }
        : {
              overviewCta: 'Browse all case studies',
              contactCta: 'Discuss a similar context',
              openerEyebrow: 'How I work',
              signageAriaLabel: 'Jump to a project',
              spreadStackLabel: 'Stack',
              trajectoryLabel: 'Trajectory',
              strengthsLabel: 'Strengths',
              focusAreasLabel: 'Focus areas',
              lookingForLabel: 'What I am looking for',
          },
);

function slugify(input: string): string {
    return input
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

type Spread = ExperienceSection & {
    id: string;
    pills: string[];
    items: string[];
};

function toSpread(section: ExperienceSection): Spread {
    const stackGroup = section.detail_groups.find(
        (group) => group.pills?.length,
    );
    const itemsGroup = section.detail_groups[0];
    return {
        ...section,
        id: slugify(section.title),
        pills: stackGroup?.pills ?? [],
        items: itemsGroup?.items ?? [],
    };
}

const allSpreads = computed<Spread[]>(() => [
    ...props.professionalSections.map(toSpread),
    ...props.associativeSections.map(toSpread),
    ...props.sideProjectSections.map(toSpread),
]);

const signageItems = computed(() =>
    allSpreads.value.map((spread) => ({
        id: spread.id,
        eyebrow: spread.eyebrow,
        label: spread.title,
    })),
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="experience-page">
            <ManifestoOpener
                :eyebrow="copy.openerEyebrow"
                :thesis="props.thesis"
                :summary="props.hero.summary"
            >
                <template #actions>
                    <Button href="/case-studies" variant="primary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </template>
            </ManifestoOpener>

            <SignageStrip
                v-if="signageItems.length"
                :items="signageItems"
                :aria-label="copy.signageAriaLabel"
            />

            <div class="experience-page__spreads">
                <EditorialSpread
                    v-for="spread in allSpreads"
                    :key="spread.id"
                    :id="spread.id"
                    :eyebrow="spread.eyebrow"
                    :title="spread.title"
                    :summary="spread.summary"
                    :paragraphs="spread.paragraphs"
                    :pills="spread.pills"
                    :items="spread.items"
                    :rail-label="copy.spreadStackLabel"
                    :marginalia="spread.marginalia"
                />
            </div>

            <DataPlate
                :trajectory="props.trajectory"
                :strengths="props.strengths"
                :focus-areas="props.focusAreas"
                :trajectory-label="copy.trajectoryLabel"
                :strengths-label="copy.strengthsLabel"
                :focus-areas-label="copy.focusAreasLabel"
            />

            <Panel class="experience-page__closer" tone="grid">
                <p class="type-eyebrow">{{ copy.lookingForLabel }}</p>
                <p class="type-body">{{ props.lookingFor }}</p>
                <div class="experience-page__closer-actions">
                    <Button href="/case-studies" variant="primary">
                        {{ copy.overviewCta }}
                    </Button>
                    <Button href="/contact" variant="ghost" arrow>
                        {{ copy.contactCta }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.experience-page {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
}

.experience-page__spreads {
    display: grid;
    gap: var(--sw-space-2xl);
    min-width: 0;
}

.experience-page__closer {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(24px, 3.2vw, 36px);
    margin-block: clamp(12px, 2vw, 20px);
    background: color-mix(
        in srgb,
        var(--sw-bg-surface) 88%,
        var(--sw-twilight-anchor) 12%
    );
    border-color: color-mix(
        in srgb,
        var(--sw-border) 64%,
        var(--sw-accent-violet) 36%
    );
}

.experience-page__closer-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

@media (max-width: 640px) {
    .experience-page {
        gap: var(--sw-space-sm);
    }

    .experience-page__spreads {
        gap: var(--sw-space-xl);
    }
}
</style>
