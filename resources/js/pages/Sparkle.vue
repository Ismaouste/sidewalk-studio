<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

type SparkleFact = {
    label: string;
    value: string;
};

type CosmicPalette = {
    accent: string;
    accentSoft: string;
    accentHot: string;
    glow: string;
    border: string;
    surface: string;
    surfaceDeep: string;
};

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    project: {
        title: string;
        summary: string;
        points: string[];
    };
    controls: {
        theme_button_on: string;
        theme_button_off: string;
        loader_button: string;
        repo_button: string;
        profile_button: string;
        palette_note: string;
        loader_note: string;
    };
    cosmicNotes: string[];
    sparkleFacts: SparkleFact[];
    repoUrl: string;
    githubProfileUrl: string | null;
}>();
const LOADER_REPLAY_EVENT = 'sidewalk:loader:replay';
const page = usePage<{ site: SiteProps }>();

const shuffleActive = ref(false);
const currentPalette = ref<CosmicPalette | null>(null);

function randomBetween(min: number, max: number): number {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function buildPalette(): CosmicPalette {
    const primaryHue = randomBetween(188, 316);
    const secondaryHue = (primaryHue + randomBetween(28, 92)) % 360;
    const tertiaryHue = (secondaryHue + randomBetween(18, 64)) % 360;

    return {
        accent: `hsl(${primaryHue} 88% 72%)`,
        accentSoft: `hsl(${secondaryHue} 74% 68%)`,
        accentHot: `hsl(${tertiaryHue} 92% 70%)`,
        glow: `hsla(${secondaryHue} 94% 72% / 0.34)`,
        border: `hsla(${primaryHue} 78% 74% / 0.28)`,
        surface: `hsla(${secondaryHue} 48% 16% / 0.72)`,
        surfaceDeep: `hsla(${primaryHue} 36% 12% / 0.88)`,
    };
}

const sparkleStyle = computed((): Record<string, string> => {
    if (currentPalette.value === null) {
        return {} as Record<string, string>;
    }

    return {
        '--sparkle-accent': currentPalette.value.accent,
        '--sparkle-accent-soft': currentPalette.value.accentSoft,
        '--sparkle-accent-hot': currentPalette.value.accentHot,
        '--sparkle-glow': currentPalette.value.glow,
        '--sparkle-border': currentPalette.value.border,
        '--sparkle-surface': currentPalette.value.surface,
        '--sparkle-surface-deep': currentPalette.value.surfaceDeep,
    };
});

const shuffleLabel = computed(() =>
    shuffleActive.value
        ? props.controls.theme_button_off
        : props.controls.theme_button_on,
);

const copy = computed(() => copyTree[page.props.site.locale].pages.sparkle);

function toggleShuffle(): void {
    if (shuffleActive.value) {
        shuffleActive.value = false;
        currentPalette.value = null;

        return;
    }

    currentPalette.value = buildPalette();
    shuffleActive.value = true;
}

function replayLoader(): void {
    if (typeof window === 'undefined') {
        return;
    }

    window.dispatchEvent(new CustomEvent(LOADER_REPLAY_EVENT));
}
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section sparkle-page" :style="sparkleStyle">
            <div class="sparkle-page__halo sparkle-page__halo--left" />
            <div class="sparkle-page__halo sparkle-page__halo--right" />

            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
            >
                <div class="sparkle-page__actions">
                    <Button @click="toggleShuffle">
                        {{ shuffleLabel }}
                    </Button>
                    <Button variant="secondary" @click="replayLoader">
                        {{ props.controls.loader_button }}
                    </Button>
                    <Button
                        :href="props.repoUrl"
                        variant="ghost"
                        arrow
                        external
                        external-mark
                    >
                        {{ props.controls.repo_button }}
                    </Button>
                </div>
            </SectionIntro>

            <div class="sparkle-page__grid">
                <Panel class="sparkle-page__panel sparkle-page__panel--hero">
                    <p class="sparkle-page__panel-title">
                        {{ props.project.title }}
                    </p>
                    <p class="type-body sparkle-page__copy">
                        {{ props.project.summary }}
                    </p>

                    <ul class="sparkle-page__list">
                        <li
                            v-for="point in props.project.points"
                            :key="point"
                            class="sparkle-page__list-item"
                        >
                            <span
                                class="sparkle-page__list-bullet"
                                aria-hidden="true"
                            />
                            <span class="type-body sparkle-page__copy">
                                {{ point }}
                            </span>
                        </li>
                    </ul>
                </Panel>

                <Panel
                    class="sparkle-page__panel sparkle-page__panel--controls"
                >
                    <p class="sparkle-page__panel-title">
                        {{ copy.controlsTitle }}
                    </p>
                    <p class="type-body sparkle-page__copy">
                        {{ props.controls.palette_note }}
                    </p>
                    <p class="type-body sparkle-page__copy">
                        {{ props.controls.loader_note }}
                    </p>

                    <div class="sparkle-page__button-stack">
                        <Button @click="toggleShuffle">
                            {{ shuffleLabel }}
                        </Button>
                        <Button variant="secondary" @click="replayLoader">
                            {{ props.controls.loader_button }}
                        </Button>
                        <Button
                            v-if="props.githubProfileUrl"
                            :href="props.githubProfileUrl"
                            variant="ghost"
                            arrow
                            external
                            external-mark
                        >
                            {{ props.controls.profile_button }}
                        </Button>
                    </div>
                </Panel>
            </div>

            <div class="sparkle-page__facts">
                <article
                    v-for="fact in props.sparkleFacts"
                    :key="fact.label"
                    class="sparkle-page__fact"
                >
                    <p class="sparkle-page__fact-label">
                        {{ fact.label }}
                    </p>
                    <p class="type-body sparkle-page__copy">
                        {{ fact.value }}
                    </p>
                </article>
            </div>

            <Panel class="sparkle-page__panel sparkle-page__panel--notes">
                <p class="sparkle-page__panel-title">
                    {{ copy.notesTitle }}
                </p>
                <div class="sparkle-page__chip-row">
                    <span
                        v-for="note in props.cosmicNotes"
                        :key="note"
                        class="sparkle-page__chip"
                    >
                        {{ note }}
                    </span>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.sparkle-page {
    position: relative;
    display: grid;
    gap: clamp(16px, 2.2vw, 24px);
    overflow: hidden;
    --sparkle-accent: hsl(286 82% 72%);
    --sparkle-accent-soft: hsl(197 76% 68%);
    --sparkle-accent-hot: hsl(34 96% 72%);
    --sparkle-glow: hsla(286 88% 72% / 0.28);
    --sparkle-border: hsla(286 72% 76% / 0.22);
    --sparkle-surface: hsla(240 26% 14% / 0.74);
    --sparkle-surface-deep: hsla(248 30% 10% / 0.88);
    --sparkle-text: hsl(0 0% 96%);
    --sparkle-muted: hsl(240 18% 78%);
    padding: clamp(20px, 3vw, 32px);
    border: 1px solid color-mix(in srgb, var(--sparkle-border) 82%, transparent);
    border-radius: var(--sw-radius-lg);
    background:
        radial-gradient(
            circle at 16% 12%,
            var(--sparkle-glow),
            transparent 30%
        ),
        radial-gradient(
            circle at 82% 18%,
            color-mix(in srgb, var(--sparkle-accent-hot) 26%, transparent),
            transparent 34%
        ),
        linear-gradient(
            140deg,
            color-mix(in srgb, var(--sparkle-surface) 88%, transparent),
            color-mix(in srgb, var(--sparkle-surface-deep) 92%, transparent)
        );
}

.sparkle-page__halo {
    position: absolute;
    inset: auto;
    width: clamp(14rem, 24vw, 20rem);
    aspect-ratio: 1;
    border-radius: var(--sw-radius-pill);
    pointer-events: none;
    filter: blur(26px);
    opacity: 0.82;
}

.sparkle-page__halo--left {
    top: -4rem;
    left: -5rem;
    background: radial-gradient(circle, var(--sparkle-glow), transparent 66%);
}

.sparkle-page__halo--right {
    right: -5rem;
    bottom: -6rem;
    background: radial-gradient(
        circle,
        color-mix(in srgb, var(--sparkle-accent-hot) 34%, transparent),
        transparent 68%
    );
}

.sparkle-page__actions,
.sparkle-page__button-stack,
.sparkle-page__chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.sparkle-page__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(18rem, 0.8fr);
    gap: clamp(14px, 2vw, 20px);
}

.sparkle-page__panel {
    position: relative;
    display: grid;
    gap: 14px;
    padding: clamp(16px, 2vw, 20px);
    border-color: color-mix(in srgb, var(--sparkle-border) 86%, transparent);
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--sparkle-surface) 86%, transparent),
        color-mix(in srgb, var(--sparkle-surface-deep) 92%, transparent)
    );
    color: var(--sparkle-text);
}

.sparkle-page__panel::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    border: 1px solid color-mix(in srgb, white 8%, transparent);
    pointer-events: none;
}

.sparkle-page__panel--hero {
    min-height: 18rem;
}

.sparkle-page__panel-title,
.sparkle-page__fact-label {
    margin: 0;
    color: var(--sparkle-accent-soft);
    font-family: var(--sw-font-code);
    font-size: 11px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.sparkle-page__copy {
    margin: 0;
    color: color-mix(in srgb, var(--sparkle-text) 84%, var(--sparkle-muted));
}

.sparkle-page__list {
    display: grid;
    gap: 12px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sparkle-page__list-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.sparkle-page__list-bullet {
    width: 7px;
    height: 7px;
    margin-top: 10px;
    flex: none;
    border-radius: var(--sw-radius-pill);
    background: linear-gradient(
        135deg,
        var(--sparkle-accent),
        var(--sparkle-accent-hot)
    );
    box-shadow: 0 0 0 4px
        color-mix(in srgb, var(--sparkle-glow) 48%, transparent);
}

.sparkle-page__facts {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(12px, 1.8vw, 18px);
}

.sparkle-page__fact {
    display: grid;
    gap: 8px;
    min-width: 0;
    padding: 14px 16px;
    border: 1px solid color-mix(in srgb, var(--sparkle-border) 78%, transparent);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sparkle-surface) 82%, transparent);
}

.sparkle-page__chip {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    padding: 0.48rem 0.72rem;
    border: 1px solid color-mix(in srgb, var(--sparkle-border) 80%, transparent);
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sparkle-surface) 68%, transparent);
    color: color-mix(in srgb, var(--sparkle-text) 84%, var(--sparkle-muted));
    font-family: var(--sw-font-body);
    font-size: 13px;
    line-height: 1.35;
}

:deep(.sparkle-page .section-intro__title),
:deep(.sparkle-page .section-intro__description) {
    color: var(--sparkle-text);
}

:deep(.sparkle-page .section-intro__eyebrow) {
    color: var(--sparkle-accent-soft);
}

@media (max-width: 960px) {
    .sparkle-page__grid,
    .sparkle-page__facts {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .sparkle-page {
        padding: 16px;
    }

    .sparkle-page__actions,
    .sparkle-page__button-stack {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
    }

    .sparkle-page__actions :deep(.sw-button),
    .sparkle-page__button-stack :deep(.sw-button) {
        width: 100%;
    }
}
</style>
