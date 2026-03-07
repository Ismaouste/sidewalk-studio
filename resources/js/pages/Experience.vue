<script setup lang="ts">
import LegendChip from '@/components/design-system/LegendChip.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    positioning: string[];
    contexts: string[];
    trajectory: Array<{
        title: string;
        summary: string;
    }>;
    strengths: string[];
    focusAreas: Array<{
        title: string;
        summary: string;
    }>;
    stackGroups: Array<{
        title: string;
        items: string[];
    }>;
    careerSnapshot: {
        title: string;
        summary: string;
        roles: string[];
    };
    cvDownloads: Array<{
        label: string;
        href: string;
    }>;
    lookingFor: string;
}>();

const trajectoryTones = ['dominant', 'green', 'coral'] as const;
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section experience-page">
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <template #actions>
                    <Button href="/projects">View projects</Button>
                    <Button href="/contact" variant="secondary">
                        Contact
                    </Button>
                </template>

                <LegendChip label="E-commerce" tone="dominant" />
                <LegendChip label="Laravel" tone="green" />
                <LegendChip label="Technical SEO" tone="sun" />
            </SectionIntro>

            <SectionDivider label="Current positioning" />

            <div class="experience-page__positioning">
                <Panel class="experience-page__panel" tone="surface">
                    <p class="type-eyebrow">How I work now</p>

                    <div class="experience-page__copy">
                        <p
                            v-for="paragraph in props.positioning"
                            :key="paragraph"
                            class="type-body experience-page__copy-line"
                        >
                            {{ paragraph }}
                        </p>
                    </div>
                </Panel>

                <Panel class="experience-page__panel" tone="grid">
                    <p class="type-eyebrow">Typical contexts</p>

                    <ul class="experience-page__list">
                        <li
                            v-for="context in props.contexts"
                            :key="context"
                            class="experience-page__list-item"
                        >
                            <LegendChip label="Context" tone="green" />
                            <p class="type-body experience-page__copy-line">
                                {{ context }}
                            </p>
                        </li>
                    </ul>
                </Panel>
            </div>

            <SectionDivider label="Professional trajectory" />

            <div class="experience-page__trajectory">
                <Panel
                    v-for="(entry, index) in props.trajectory"
                    :key="entry.title"
                    class="experience-page__trajectory-card"
                    tone="surface"
                >
                    <LegendChip
                        :label="`Phase 0${index + 1}`"
                        :tone="trajectoryTones[index] ?? 'violet'"
                    />
                    <h2 class="type-h2 experience-page__title">
                        {{ entry.title }}
                    </h2>
                    <p class="type-body experience-page__copy-line">
                        {{ entry.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider label="Selected technical focus" />

            <div class="experience-page__focus-grid">
                <Panel
                    v-for="focus in props.focusAreas"
                    :key="focus.title"
                    class="experience-page__focus-card"
                    tone="grid"
                >
                    <h2 class="type-h2 experience-page__title">
                        {{ focus.title }}
                    </h2>
                    <p class="type-body experience-page__copy-line">
                        {{ focus.summary }}
                    </p>
                </Panel>
            </div>

            <SectionDivider label="Strengths and environments" />

            <div class="experience-page__working-grid">
                <Panel class="experience-page__panel" tone="surface">
                    <p class="type-eyebrow">Strengths and working style</p>

                    <ul class="experience-page__list">
                        <li
                            v-for="strength in props.strengths"
                            :key="strength"
                            class="experience-page__list-item"
                        >
                            <LegendChip label="Strength" tone="dominant" />
                            <p class="type-body experience-page__copy-line">
                                {{ strength }}
                            </p>
                        </li>
                    </ul>
                </Panel>

                <Panel class="experience-page__panel" tone="grid">
                    <p class="type-eyebrow">Stack and environments</p>

                    <div class="experience-page__stack-groups">
                        <section
                            v-for="group in props.stackGroups"
                            :key="group.title"
                            class="experience-page__stack-group"
                        >
                            <h2 class="type-nav experience-page__stack-title">
                                {{ group.title }}
                            </h2>
                            <div class="experience-page__stack-items">
                                <span
                                    v-for="item in group.items"
                                    :key="item"
                                    class="type-meta experience-page__stack-item"
                                >
                                    {{ item }}
                                </span>
                            </div>
                        </section>
                    </div>
                </Panel>
            </div>

            <SectionDivider label="What I am looking for" />

            <div class="experience-page__closing-grid">
                <Panel class="experience-page__closing" tone="surface">
                    <p class="type-body experience-page__copy-line">
                        {{ props.lookingFor }}
                    </p>

                    <div class="experience-page__actions">
                        <Button href="/projects">Projects</Button>
                        <Button href="/writing" variant="secondary">
                            Writing
                        </Button>
                        <Button href="/contact" variant="ghost">
                            Contact
                        </Button>
                    </div>
                </Panel>

                <Panel class="experience-page__closing" tone="grid">
                    <p class="type-eyebrow">
                        {{ props.careerSnapshot.title }}
                    </p>
                    <p class="type-body experience-page__copy-line">
                        {{ props.careerSnapshot.summary }}
                    </p>

                    <div class="experience-page__stack-items">
                        <span
                            v-for="role in props.careerSnapshot.roles"
                            :key="role"
                            class="type-meta experience-page__stack-item"
                        >
                            {{ role }}
                        </span>
                    </div>

                    <div class="experience-page__actions">
                        <Button
                            v-for="download in props.cvDownloads"
                            :key="download.href"
                            :href="download.href"
                        >
                            {{ download.label }}
                        </Button>
                    </div>
                </Panel>
            </div>
        </section>
    </SiteLayout>
</template>

<style scoped>
.experience-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.experience-page__positioning,
.experience-page__working-grid,
.experience-page__closing-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
}

.experience-page__panel,
.experience-page__trajectory-card,
.experience-page__focus-card,
.experience-page__closing {
    display: grid;
    gap: var(--sw-space-sm);
    padding: var(--sw-space-sm);
}

.experience-page__copy {
    display: grid;
    gap: var(--sw-space-xs);
}

.experience-page__copy-line,
.experience-page__title {
    margin: 0;
}

.experience-page__copy-line {
    color: var(--sw-text-secondary);
}

.experience-page__title {
    color: var(--sw-text-primary);
}

.experience-page__trajectory,
.experience-page__focus-grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.experience-page__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.experience-page__list-item {
    display: grid;
    gap: var(--sw-space-3xs);
    border-top: 1px solid var(--sw-border);
    padding-top: var(--sw-space-xs);
}

.experience-page__list-item:first-child {
    border-top: 0;
    padding-top: 0;
}

.experience-page__stack-groups {
    display: grid;
    gap: var(--sw-space-sm);
}

.experience-page__stack-group {
    display: grid;
    gap: var(--sw-space-xs);
}

.experience-page__stack-title {
    color: var(--sw-text-primary);
}

.experience-page__stack-items {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.experience-page__stack-item {
    display: inline-flex;
    align-items: center;
    min-height: 1.75rem;
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 72%, transparent);
    padding-inline: var(--sw-space-2xs);
}

.experience-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
}

@media (max-width: 960px) {
    .experience-page__positioning,
    .experience-page__working-grid,
    .experience-page__closing-grid,
    .experience-page__trajectory,
    .experience-page__focus-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
