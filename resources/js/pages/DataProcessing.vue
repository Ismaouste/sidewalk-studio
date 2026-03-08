<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import Panel from '@/components/ui/Panel.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    storage: {
        eyebrow: string;
        title: string;
        points: string[];
    };
    consent: {
        eyebrow: string;
        title: string;
        points: string[];
    };
    operator: {
        eyebrow: string;
        title: string;
        summary: string;
    };
}>();

const page = usePage<{ site: SiteProps }>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="seo" />

        <section class="sw-section data-processing-page">
            <SectionIntro
                :eyebrow="hero.eyebrow"
                :title="hero.title"
                :description="hero.summary"
            >
                <template #actions>
                    <ConsentPreferencesButton />
                </template>
            </SectionIntro>

            <SectionDivider :label="hero.eyebrow" />

            <div class="data-processing-page__grid">
                <Panel class="data-processing-page__panel" tone="surface">
                    <p class="type-eyebrow">{{ storage.eyebrow }}</p>
                    <h2 class="type-h2 data-processing-page__title">
                        {{ storage.title }}
                    </h2>
                    <ul class="data-processing-page__list">
                        <li
                            v-for="point in storage.points"
                            :key="point"
                            class="type-body"
                        >
                            {{ point }}
                        </li>
                    </ul>
                </Panel>

                <Panel class="data-processing-page__panel" tone="grid">
                    <p class="type-eyebrow">{{ consent.eyebrow }}</p>
                    <h2 class="type-h2 data-processing-page__title">
                        {{ consent.title }}
                    </h2>
                    <ul class="data-processing-page__list">
                        <li
                            v-for="point in consent.points"
                            :key="point"
                            class="type-body"
                        >
                            {{ point }}
                        </li>
                    </ul>
                </Panel>
            </div>

            <Panel class="data-processing-page__panel" tone="surface">
                <p class="type-eyebrow">{{ operator.eyebrow }}</p>
                <h2 class="type-h2 data-processing-page__title">
                    {{ operator.title }}
                </h2>
                <p class="type-body data-processing-page__summary">
                    {{ operator.summary }}
                </p>
                <a
                    class="data-processing-page__contact"
                    :href="`mailto:${page.props.site.contact.email}`"
                    rel="nofollow"
                >
                    {{ page.props.site.contact.email }}
                </a>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.data-processing-page {
    display: grid;
    gap: var(--sw-space-sm);
}

.data-processing-page__grid {
    display: grid;
    gap: var(--sw-space-sm);
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.data-processing-page__panel {
    display: grid;
    gap: var(--sw-space-xs);
    padding: clamp(18px, 2.8vw, var(--sw-space-sm));
}

.data-processing-page__title,
.data-processing-page__summary,
.data-processing-page__list {
    margin: 0;
}

.data-processing-page__list {
    display: grid;
    gap: var(--sw-space-2xs);
    padding-left: 1.15rem;
    color: var(--sw-text-secondary);
}

.data-processing-page__contact {
    width: fit-content;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.18em;
}

@media (max-width: 768px) {
    .data-processing-page__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
