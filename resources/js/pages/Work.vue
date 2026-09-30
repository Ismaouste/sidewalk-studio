<script setup lang="ts">
/**
 * Work: the public projects, one lot each (specs/018-site-refresh). Plain and
 * factual on purpose: what it is, what was done, one link. A lot with no
 * public address and no approved wording is a name and a kind, nothing more.
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import { copy as copyTree } from '@/copy';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteProps } from '@/types';

interface Lot {
    kind: string;
    title: string;
    summary?: string;
    role?: string;
    href?: string;
    cta?: string;
}

const props = defineProps<{
    seo: SeoPayload;
    hero: { eyebrow: string; title: string; summary: string };
    lotsIntro: { eyebrow: string; title: string; summary: string };
    lots: Lot[];
    revision: string;
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].pages.work);

const numbered = computed(() =>
    props.lots.map((lot, index) => ({
        ...lot,
        number: String(index + 1).padStart(2, '0'),
        external: !!lot.href && /^https?:\/\//.test(lot.href),
    })),
);
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="sw-section sw-section--hero work-hero">
            <p class="type-meta work-hero__revision">
                {{ copy.revisionLabel }} · {{ props.revision }}
            </p>
            <SectionIntro
                :eyebrow="props.hero.eyebrow"
                :title="props.hero.title"
                :description="props.hero.summary"
                size="hero"
            />
        </section>

        <section class="sw-section work-lots" aria-labelledby="work-lots-title">
            <SectionIntro
                :eyebrow="props.lotsIntro.eyebrow"
                :title="props.lotsIntro.title"
                :description="props.lotsIntro.summary"
            />
            <h2 id="work-lots-title" class="work-lots__sr">
                {{ props.lotsIntro.title }}
            </h2>

            <ol class="work-lots__list">
                <li v-for="lot in numbered" :key="lot.title" class="work-lot">
                    <p class="type-meta work-lot__index">
                        {{ copy.lotLabel }} {{ lot.number }} · {{ lot.kind }}
                    </p>
                    <h3 class="work-lot__title">{{ lot.title }}</h3>
                    <p v-if="lot.summary" class="type-body work-lot__summary">
                        {{ lot.summary }}
                    </p>
                    <p v-if="lot.role" class="type-body work-lot__role">
                        <span class="type-meta work-lot__role-label">
                            {{ copy.roleLabel }}
                        </span>
                        {{ lot.role }}
                    </p>
                    <p class="work-lot__action">
                        <a
                            v-if="lot.href"
                            class="work-lot__link"
                            :href="lot.href"
                            :target="lot.external ? '_blank' : undefined"
                            :rel="lot.external ? 'noopener' : undefined"
                        >
                            {{ lot.cta ?? lot.href }}
                        </a>
                        <span v-else class="type-meta work-lot__none">
                            {{ copy.noLink }}
                        </span>
                    </p>
                </li>
            </ol>
        </section>
    </SiteLayout>
</template>

<style scoped>
.work-hero {
    display: grid;
    gap: var(--sw-space-2xs);
}

.work-hero__revision {
    margin: 0;
    color: var(--sw-text-muted);
}

.work-lots {
    display: grid;
    gap: var(--sw-space-sm);
}

.work-lots__sr {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    overflow: hidden;
    clip-path: inset(50%);
}

.work-lots__list {
    display: grid;
    gap: 0;
    margin: 0;
    padding: 0;
    list-style: none;
    border-top: var(--sw-hairline) solid var(--sw-border);
}

.work-lot {
    display: grid;
    gap: var(--sw-space-2xs);
    padding-block: var(--sw-space-sm);
    border-bottom: var(--sw-hairline) solid var(--sw-border);
}

.work-lot__index {
    margin: 0;
    color: var(--sw-text-muted);
}

.work-lot__title {
    margin: 0;
    font-family: var(--sw-font-heading);
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 600;
    letter-spacing: -0.02em;
}

.work-lot__summary,
.work-lot__role {
    margin: 0;
    max-width: 68ch;
}

.work-lot__role {
    color: var(--sw-text-secondary);
}

.work-lot__role-label {
    display: block;
    margin-bottom: var(--sw-space-4xs);
    color: var(--sw-text-muted);
}

.work-lot__action {
    margin: var(--sw-space-3xs) 0 0;
}

.work-lot__link {
    display: inline-block;
    padding: var(--sw-space-3xs) var(--sw-space-xs);
    border: var(--sw-hairline) solid var(--sw-text-primary);
    color: var(--sw-text-primary);
    font-family: var(--sw-font-code);
    font-size: 12px;
    letter-spacing: 0.06em;
    text-decoration: none;
    text-transform: uppercase;
    transition:
        background-color var(--sw-motion-fast),
        color var(--sw-motion-fast);
}

.work-lot__link:hover,
.work-lot__link:focus-visible {
    background: var(--sw-text-primary);
    color: var(--sw-bg-base);
}

.work-lot__none {
    color: var(--sw-text-muted);
}
</style>
