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
    color: var(--sw-accent-violet);
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
