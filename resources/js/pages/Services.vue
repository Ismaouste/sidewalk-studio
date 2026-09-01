<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { capture } from '@/lib/analytics';
import type { SeoPayload, SiteProps } from '@/types';

export interface ServiceOffer {
    label: string;
    title: string;
    summary: string;
    price: string;
    price_meta?: string;
    points: string[];
    cta: string;
    tone: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
}

defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    offers: ServiceOffer[];
    modifiers: {
        title: string;
        summary: string;
    };
    engagement: {
        title: string;
        steps: Array<{ title: string; summary: string }>;
    };
    legalNote: string;
    contactCta: {
        title: string;
        summary: string;
    };
    cvDownloads: Array<{ label: string; href: string }>;
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].pages.services);

onMounted(() => {
    capture('services_viewed', { funnel_stage: 'V2' });
});
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="seo" />

        <section class="sw-section services-page">
            <SectionIntro
                :eyebrow="hero.eyebrow"
                :title="hero.title"
                :description="hero.summary"
            />

            <div class="services-page__grid">
                <!--
                    The tariff board. Each offer reads like a line on a café
                    card: name on the left, a dotted leader, the price on the
                    right in the display face. The structure is the message —
                    the prices are on the door.
                -->
                <ol class="services-board">
                    <li
                        v-for="offer in offers"
                        :key="offer.title"
                        :class="`services-tone--${offer.tone}`"
                        class="services-entry"
                    >
                        <p class="type-eyebrow services-entry__label">
                            {{ offer.label }}
                        </p>
                        <div class="services-entry__head">
                            <h2 class="type-h3 services-entry__title">
                                {{ offer.title }}
                            </h2>
                            <span
                                aria-hidden="true"
                                class="services-entry__leader"
                            ></span>
                            <p class="services-entry__price-block">
                                <span class="services-entry__price">{{
                                    offer.price
                                }}</span>
                                <span
                                    v-if="offer.price_meta"
                                    class="type-body-sm services-entry__price-meta"
                                >
                                    {{ offer.price_meta }}
                                </span>
                            </p>
                        </div>
                        <p class="type-body services-entry__summary">
                            {{ offer.summary }}
                        </p>
                        <details class="services-entry__details">
                            <summary
                                class="type-meta services-entry__details-summary"
                            >
                                {{ copy.includedLabel }}
                            </summary>
                            <ul class="services-entry__points">
                                <li
                                    v-for="point in offer.points"
                                    :key="point"
                                    class="type-body-sm"
                                >
                                    {{ point }}
                                </li>
                            </ul>
                        </details>
                        <div class="services-entry__cta">
                            <Button href="/contact" variant="ghost">
                                {{ offer.cta }}
                            </Button>
                        </div>
                    </li>
                </ol>

                <aside class="services-page__aside">
                    <Panel class="services-aside__panel" tone="grid">
                        <h2 class="type-h3 services-aside__title">
                            {{ modifiers.title }}
                        </h2>
                        <p class="type-body-sm services-aside__summary">
                            {{ modifiers.summary }}
                        </p>
                    </Panel>
                    <p class="type-body-sm services-page__legal">
                        {{ legalNote }}
                    </p>
                </aside>
            </div>

            <div class="services-engagement">
                <h2 class="type-h2 services-engagement__title">
                    {{ engagement.title }}
                </h2>
                <ol class="services-steps">
                    <li
                        v-for="step in engagement.steps"
                        :key="step.title"
                        class="services-steps__item"
                    >
                        <h3 class="type-h3 services-steps__title">
                            {{ step.title }}
                        </h3>
                        <p class="type-body services-steps__summary">
                            {{ step.summary }}
                        </p>
                    </li>
                </ol>
            </div>

            <Panel class="services-contact" tone="elevated">
                <div class="services-contact__copy">
                    <p class="type-eyebrow services-contact__eyebrow">
                        {{ copy.contactLabel }}
                    </p>
                    <h2 class="type-h2 services-contact__title">
                        {{ contactCta.title }}
                    </h2>
                    <p class="type-body services-contact__summary">
                        {{ contactCta.summary }}
                    </p>
                </div>
                <div class="services-contact__actions">
                    <Button href="/contact">{{ copy.contactCta }}</Button>
                    <Button
                        v-for="download in cvDownloads"
                        :key="download.href"
                        :href="download.href"
                        variant="ghost"
                    >
                        {{ download.label }}
                    </Button>
                </div>
            </Panel>
        </section>
    </SiteLayout>
</template>

<style scoped>
.services-page__grid {
    display: grid;
    gap: var(--sw-space-md);
    margin-top: var(--sw-space-md);
}

@media (min-width: 1024px) {
    .services-page__grid {
        grid-template-columns: minmax(0, 7fr) minmax(0, 3fr);
        gap: var(--sw-space-lg);
    }

    .services-page__aside {
        position: sticky;
        top: var(--sw-header-offset);
        align-self: start;
    }
}

.services-board {
    display: grid;
    gap: var(--sw-space-sm);
    margin: 0;
    padding: 0;
    list-style: none;
}

.services-entry {
    --services-accent: var(--sw-accent-dominant);

    display: grid;
    gap: var(--sw-space-2xs);
    padding-top: var(--sw-space-sm);
    border-top: 1px solid
        color-mix(in srgb, var(--services-accent) 24%, var(--sw-border));
}

.services-tone--dominant {
    --services-accent: var(--sw-accent-dominant);
}

.services-tone--violet {
    --services-accent: var(--sw-accent-violet);
}

.services-tone--coral {
    --services-accent: var(--sw-accent-coral);
}

.services-tone--sun {
    --services-accent: var(--sw-accent-sun);
}

.services-tone--green {
    --services-accent: var(--sw-accent-green);
}

.services-entry__label {
    width: fit-content;
    color: var(--services-accent);
}

.services-entry__head {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: var(--sw-space-3xs) var(--sw-space-xs);
}

.services-entry__title {
    margin: 0;
    min-width: 0;
}

/*
 * The dotted leader between an offer and its price — the café-card idiom.
 * It only earns its keep when name and price share a line, so it collapses
 * on narrow screens where the price wraps underneath.
 */
.services-entry__leader {
    display: none;
}

@media (min-width: 640px) {
    .services-entry__leader {
        display: block;
        flex: 1 1 3rem;
        min-width: 2.5rem;
        align-self: flex-end;
        margin-bottom: 0.5em;
        border-bottom: 3px dotted
            color-mix(in srgb, var(--services-accent) 45%, var(--sw-border));
    }
}

.services-entry__price-block {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin: 0;
}

@media (min-width: 640px) {
    .services-entry__price-block {
        align-items: flex-end;
        text-align: right;
    }
}

/*
 * The price keeps the offer's hue but leans toward ink: the raw sun accent
 * on the morning ground measures 2.95:1, below the 3:1 large-text floor,
 * and the price is the one thing this page must never make hard to read.
 */
.services-entry__price {
    font-family: var(--sw-font-display);
    font-style: italic;
    font-weight: 300;
    font-size: clamp(1.5rem, 1.1rem + 1.8vw, 2.2rem);
    line-height: 1.1;
    color: color-mix(
        in srgb,
        var(--services-accent) 72%,
        var(--sw-text-primary)
    );
}

.services-entry__price-meta {
    color: var(--sw-text-muted);
}

.services-entry__summary {
    margin: 0;
    max-width: 62ch;
}

.services-entry__details {
    max-width: 62ch;
}

.services-entry__details-summary {
    display: inline-flex;
    align-items: center;
    gap: var(--sw-space-4xs);
    width: fit-content;
    cursor: pointer;
    list-style: none;
}

.services-entry__details-summary::-webkit-details-marker {
    display: none;
}

.services-entry__details-summary::before {
    content: '+';
    font-family: var(--sw-font-code);
    color: var(--services-accent);
    transition: rotate var(--sw-motion-fast);
}

.services-entry__details[open] .services-entry__details-summary::before {
    rotate: 45deg;
}

.services-entry__details-summary:hover,
.services-entry__details-summary:focus-visible {
    color: var(--sw-text-primary);
}

.services-entry__points {
    display: grid;
    gap: var(--sw-space-4xs);
    margin: var(--sw-space-3xs) 0 0;
    padding-left: 1.1em;
}

.services-entry__cta {
    justify-self: start;
}

.services-page__aside {
    display: grid;
    gap: var(--sw-space-xs);
}

.services-aside__panel {
    display: grid;
    gap: var(--sw-space-3xs);
}

.services-aside__title,
.services-aside__summary {
    margin: 0;
}

.services-page__legal {
    margin: 0;
    color: var(--sw-text-muted);
}

.services-engagement {
    display: grid;
    gap: var(--sw-space-sm);
    margin-top: var(--sw-space-lg);
}

.services-engagement__title {
    margin: 0;
}

.services-steps {
    display: grid;
    gap: var(--sw-space-sm);
    margin: 0;
    padding: 0;
    list-style: none;
    counter-reset: services-step;
}

@media (min-width: 768px) {
    .services-steps {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: var(--sw-space-md);
    }
}

.services-steps__item {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: var(--sw-space-4xs) var(--sw-space-xs);
    counter-increment: services-step;
}

/*
 * Real sequence, so the numbering carries information: this is the order an
 * engagement actually runs in, not decoration.
 */
.services-steps__item::before {
    content: counter(services-step, decimal-leading-zero);
    grid-row: 1 / span 2;
    font-family: var(--sw-font-code);
    font-size: 0.8rem;
    padding-top: 0.5em;
    color: var(--sw-text-muted);
}

.services-steps__title {
    margin: 0;
}

.services-steps__summary {
    margin: 0;
    grid-column: 2;
}

.services-contact {
    display: grid;
    gap: var(--sw-space-sm);
    margin-top: var(--sw-space-lg);
}

.services-contact__copy {
    display: grid;
    gap: var(--sw-space-3xs);
}

.services-contact__eyebrow {
    width: fit-content;
}

.services-contact__title,
.services-contact__summary {
    margin: 0;
}

.services-contact__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
}

@supports (animation-timeline: view()) {
    @media (prefers-reduced-motion: no-preference) {
        .services-entry {
            animation: services-entry-reveal linear both;
            animation-timeline: view();
            animation-range: entry 5% cover 28%;
        }
    }
}

@keyframes services-entry-reveal {
    from {
        opacity: 0;
        transform: translateY(14px);
    }

    to {
        opacity: 1;
        transform: none;
    }
}
</style>
