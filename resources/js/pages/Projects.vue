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
import { copy as copyTree } from '@/copy';
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
    marginalia?: { quote: string; author?: string; prompt?: string };
};

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
    hobbies?: string[];
    lookingFor: string;
    cvDownloads: Array<{ label: string; href: string }>;
}>();

const page = usePage<{ site: SiteProps }>();

const copy = computed(() => copyTree[page.props.site.locale].pages.projects);
const landmarks = computed(
    () => copyTree[page.props.site.locale].layout.landmarks,
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
    role: string;
    dateRange: string;
};

function splitEyebrow(eyebrow: string): { role: string; dateRange: string } {
    const parts = eyebrow.split(/\s+[—–-]\s+/);
    if (parts.length >= 2) {
        return {
            role: parts[0]!.trim(),
            dateRange: parts.slice(1).join(' — ').trim(),
        };
    }
    return { role: eyebrow.trim(), dateRange: '' };
}

function toSpread(section: ExperienceSection): Spread {
    const stackGroup = section.detail_groups.find(
        (group) => group.pills?.length,
    );
    const itemsGroup = section.detail_groups[0];
    const { role, dateRange } = splitEyebrow(section.eyebrow);
    return {
        ...section,
        id: slugify(section.title),
        pills: stackGroup?.pills ?? [],
        items: itemsGroup?.items ?? [],
        role,
        dateRange,
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
        eyebrow: spread.role,
        label: spread.title,
        dateRange: spread.dateRange,
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
                    :id="spread.id"
                    :key="spread.id"
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

            <section
                v-if="props.hobbies?.length"
                class="experience-page__hobbies"
                aria-labelledby="experience-hobbies"
            >
                <p
                    id="experience-hobbies"
                    class="type-eyebrow experience-page__hobbies-label"
                >
                    {{ copy.hobbiesLabel }}
                </p>
                <div class="experience-page__hobbies-pills">
                    <span
                        v-for="hobby in props.hobbies"
                        :key="hobby"
                        class="type-meta experience-page__hobby"
                    >
                        {{ hobby }}
                    </span>
                </div>
            </section>

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

            <nav
                class="experience-page__nudge"
                :aria-label="landmarks.nextStep"
            >
                <Button href="/journal" variant="ghost" arrow>
                    {{ copy.nudgeJournalCta }}
                </Button>
            </nav>
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
        var(--sw-border) 56%,
        var(--sw-accent-sun) 44%
    );
}

.experience-page__closer-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.experience-page__hobbies {
    display: grid;
    gap: var(--sw-space-3xs);
    padding: var(--sw-space-sm) 0;
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
}

.experience-page__hobbies-label {
    margin: 0;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 84%,
        var(--sw-text-primary)
    );
}

.experience-page__hobbies-pills {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.experience-page__hobby {
    display: inline-flex;
    align-items: center;
    padding: 0.22rem 0.7rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 70%, transparent);
    border-radius: 999px;
    background: color-mix(in srgb, var(--sw-bg-surface) 60%, transparent);
    color: var(--sw-text-secondary);
}

.experience-page__nudge {
    display: flex;
    justify-content: flex-end;
    padding-top: var(--sw-space-xs);
}

@media (max-width: 640px) {
    .experience-page {
        gap: var(--sw-space-sm);
    }

    .experience-page__spreads {
        gap: var(--sw-space-xl);
    }

    .experience-page__nudge {
        justify-content: stretch;
    }

    .experience-page__nudge :deep(.sw-button) {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
