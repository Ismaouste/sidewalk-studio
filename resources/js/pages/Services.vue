<script setup lang="ts">
/**
 * Services: the stacks I work with, and starting prices (Ismaël, 2026-10-01:
 * "say the stacks I master, give ranges 'from', no italic price, no serif;
 * show prices only once booking and payment are configured"). No booking and no
 * payment button on this page until they exist: a single link to the contact
 * page. Prices are starting points, set in the sans face, upright.
 */
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import Button from '@/components/ui/Button.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { capture } from '@/lib/analytics';
import type { SeoPayload, SiteProps } from '@/types';

interface ServiceOffer {
    title: string;
    summary?: string;
    price: string;
    price_meta?: string;
}

defineProps<{
    seo: SeoPayload;
    hero: { eyebrow: string; title: string; summary: string };
    stacks: Array<{ title: string; items: string[] }>;
    offers: ServiceOffer[];
    legalNote: string;
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

            <div class="services-block" aria-labelledby="services-stacks">
                <h2 id="services-stacks" class="type-eyebrow">
                    {{ copy.stacksLabel }}
                </h2>
                <dl class="services-stacks">
                    <div
                        v-for="group in stacks"
                        :key="group.title"
                        class="services-stacks__group"
                    >
                        <dt>{{ group.title }}</dt>
                        <dd>{{ group.items.join(' / ') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="services-block" aria-labelledby="services-ranges">
                <h2 id="services-ranges" class="type-eyebrow">
                    {{ copy.rangesLabel }}
                </h2>
                <ol class="services-ranges">
                    <li
                        v-for="offer in offers"
                        :key="offer.title"
                        class="services-ranges__row"
                    >
                        <div class="services-ranges__text">
                            <h3 class="services-ranges__title">
                                {{ offer.title }}
                            </h3>
                            <p v-if="offer.summary" class="type-body-sm">
                                {{ offer.summary }}
                            </p>
                        </div>
                        <p class="services-ranges__price">
                            <span class="services-ranges__amount">{{
                                offer.price
                            }}</span>
                            <span
                                v-if="offer.price_meta"
                                class="services-ranges__meta"
                                >{{ offer.price_meta }}</span
                            >
                        </p>
                    </li>
                </ol>
                <p class="type-body-sm services-page__legal">
                    {{ legalNote }}
                </p>
            </div>

            <div class="services-page__actions">
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
        </section>
    </SiteLayout>
</template>

<style scoped>
.services-page {
    display: grid;
    gap: var(--sw-space-md);
}

.services-block {
    display: grid;
    gap: var(--sw-space-xs);
    padding-top: var(--sw-space-sm);
    border-top: var(--sw-hairline) solid var(--sw-border);
}

.services-block > h2 {
    margin: 0;
}

/* Stacks: a label, then the list, set like the notes on a drawing. */
.services-stacks {
    display: grid;
    gap: 0;
    margin: 0;
}

.services-stacks__group {
    display: grid;
    gap: var(--sw-space-4xs);
    padding-block: var(--sw-space-2xs);
    border-bottom: var(--sw-hairline) solid var(--sw-border);
}

.services-stacks dt {
    color: var(--sw-text-muted);
    font-family: var(--sw-font-code);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.services-stacks dd {
    margin: 0;
    font-family: var(--sw-font-code);
    font-size: 13px;
    line-height: 1.6;
}

@media (min-width: 720px) {
    .services-stacks__group {
        grid-template-columns: 14rem 1fr;
        gap: var(--sw-space-sm);
    }
}

/* Ranges: name on the left, "from" price on the right. Upright, sans, no serif. */
.services-ranges {
    display: grid;
    gap: 0;
    margin: 0;
    padding: 0;
    list-style: none;
}

.services-ranges__row {
    display: grid;
    gap: var(--sw-space-3xs);
    padding-block: var(--sw-space-xs);
    border-bottom: var(--sw-hairline) solid var(--sw-border);
}

.services-ranges__title {
    margin: 0;
    font-family: var(--sw-font-heading);
    font-size: 1.1rem;
    font-weight: 600;
}

.services-ranges__text p {
    margin: var(--sw-space-4xs) 0 0;
    max-width: 56ch;
    color: var(--sw-text-secondary);
}

.services-ranges__price {
    display: grid;
    gap: 2px;
    margin: 0;
}

.services-ranges__amount {
    font-family: var(--sw-font-heading);
    font-size: 1.25rem;
    font-style: normal;
    font-weight: 600;
    line-height: 1.2;
}

.services-ranges__meta {
    color: var(--sw-text-muted);
    font-family: var(--sw-font-code);
    font-size: 11px;
    letter-spacing: 0.04em;
}

@media (min-width: 720px) {
    .services-ranges__row {
        grid-template-columns: 1fr auto;
        align-items: baseline;
        gap: var(--sw-space-md);
    }

    .services-ranges__price {
        justify-items: end;
        text-align: right;
    }
}

.services-page__legal {
    margin: var(--sw-space-2xs) 0 0;
    color: var(--sw-text-muted);
}

.services-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
}
</style>
