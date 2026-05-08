<script setup lang="ts">
import ManifestoOpener from '@/components/experience/ManifestoOpener.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

type ColophonSection = {
    title: string;
    eyebrow: string;
    summary: string;
    cta_label: string;
    cta_href: string;
};

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    sections: ColophonSection[];
    closing: ColophonSection;
}>();

const isExternal = (href: string) => /^https?:\/\//.test(href);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="colophon-page">
            <ManifestoOpener
                :eyebrow="props.hero.eyebrow"
                :thesis="props.hero.title"
                :summary="props.hero.summary"
            />

            <div class="colophon-page__sections">
                <Panel
                    v-for="section in props.sections"
                    :key="section.title"
                    class="colophon-page__row"
                    tone="surface"
                >
                    <p class="type-eyebrow colophon-page__eyebrow">
                        {{ section.eyebrow }}
                    </p>
                    <h2 class="type-h2 colophon-page__title">
                        {{ section.title }}
                    </h2>
                    <p class="type-body colophon-page__summary">
                        {{ section.summary }}
                    </p>
                    <div class="colophon-page__action">
                        <Button
                            :href="section.cta_href"
                            :external="isExternal(section.cta_href)"
                            variant="ghost"
                            arrow
                        >
                            {{ section.cta_label }}
                        </Button>
                    </div>
                </Panel>
            </div>

            <Panel class="colophon-page__closing" tone="grid">
                <p class="type-eyebrow">{{ props.closing.eyebrow }}</p>
                <h2 class="type-h1 colophon-page__closing-title">
                    {{ props.closing.title }}
                </h2>
                <p class="type-body">{{ props.closing.summary }}</p>
                <div class="colophon-page__action">
                    <Button
                        :href="props.closing.cta_href"
                        :external="isExternal(props.closing.cta_href)"
                        variant="primary"
                        arrow
                    >
                        {{ props.closing.cta_label }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.colophon-page {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
    max-width: 64rem;
    position: relative;
    isolation: isolate;
}

.colophon-page::before {
    content: '';
    position: absolute;
    inset: -8vh -12vw 30% -12vw;
    z-index: -1;
    background:
        radial-gradient(
            ellipse 70% 60% at 50% 0%,
            color-mix(in oklch, var(--sw-twilight-sky) 32%, transparent),
            transparent 72%
        ),
        radial-gradient(
            ellipse 40% 30% at 18% 8%,
            color-mix(in oklch, var(--sw-twilight-sky) 22%, transparent),
            transparent 68%
        ),
        radial-gradient(
            ellipse 35% 25% at 82% 12%,
            color-mix(in oklch, var(--sw-twilight-glow) 18%, transparent),
            transparent 70%
        );
    pointer-events: none;
    filter: blur(36px);
    transform-origin: 50% 0%;
    will-change: transform;
}

@supports (animation-timeline: scroll()) {
    @media (prefers-reduced-motion: no-preference) {
        .colophon-page::before {
            animation: colophon-sky-drift 26s ease-in-out infinite alternate;
        }
    }
}

@media (prefers-reduced-motion: reduce) {
    .colophon-page::before {
        animation: none;
    }
}

@keyframes colophon-sky-drift {
    from {
        transform: translate3d(-1.2%, 0, 0) scale(1);
        opacity: 0.82;
    }
    to {
        transform: translate3d(1.5%, -0.8%, 0) scale(1.04);
        opacity: 1;
    }
}

.colophon-page__sections {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
}

.colophon-page__row {
    display: grid;
    gap: var(--sw-space-3xs);
    padding: clamp(20px, 2.4vw, 28px);
}

.colophon-page__eyebrow {
    color: color-mix(
        in oklch,
        var(--sw-twilight-sky) 72%,
        var(--sw-text-secondary) 28%
    );
}

.colophon-page__title,
.colophon-page__closing-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.colophon-page__summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
    max-width: 56rem;
}

.colophon-page__action {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    padding-top: var(--sw-space-3xs);
}

.colophon-page__closing {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(24px, 3.2vw, 36px);
    margin-block-start: clamp(8px, 1.6vw, 16px);
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

@media (max-width: 640px) {
    .colophon-page {
        gap: var(--sw-space-sm);
    }

    .colophon-page__row {
        padding: var(--sw-space-sm);
    }
}
</style>
