<script setup lang="ts">
/**
 * The plan of the work, on the home page (specs/018-site-refresh): the public
 * projects and the services drawn as lots, with the interfaces between them.
 * Three arrangements of one plan: a portrait strip first (phones), a landscape
 * sheet from 720 px, and from 1400 px the same sheet with the career branch
 * (Jewely, Aremedia, Parcours le Monde) on its left. Only one is displayed; the
 * others are `display: none`, so they are out of the accessibility tree too.
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { copy as copyTree } from '@/copy';
import { localizePublicHref } from '@/lib/publicHref';
import type { SiteProps } from '@/types';
import { buildPlan } from './planLayout';
import Schema from './Schema.vue';

defineOptions({ name: 'HomePlan' });

const page = usePage<{ site: SiteProps }>();
const locale = computed(() => page.props.site.locale);
const plan = computed(() => copyTree[locale.value].pages.home.plan);
const localize = (href: string) => localizePublicHref(href, locale.value);

const compact = computed(() => buildPlan(plan.value, 'compact', localize));
const wide = computed(() => buildPlan(plan.value, 'wide', localize));
const ultra = computed(() => buildPlan(plan.value, 'ultra', localize));
</script>

<template>
    <section class="sw-section home-plan" aria-labelledby="home-plan-title">
        <p class="type-meta home-plan__revision">{{ plan.revision }}</p>
        <h2 id="home-plan-title" class="type-h2">{{ plan.title }}</h2>
        <p class="type-body home-plan__caption">{{ plan.caption }}</p>

        <Schema
            class="home-plan__sheet home-plan__sheet--compact"
            :data="compact"
        />
        <Schema class="home-plan__sheet home-plan__sheet--wide" :data="wide" />
        <Schema
            class="home-plan__sheet home-plan__sheet--ultra"
            :data="ultra"
        />

        <ul class="type-meta home-plan__legend">
            <li v-for="line in plan.legend" :key="line">{{ line }}</li>
        </ul>
    </section>
</template>

<style scoped>
.home-plan {
    display: grid;
    gap: var(--sw-space-xs);
    padding-block: var(--sw-space-md);
    border-top: var(--sw-hairline) solid var(--sw-border);
}

.home-plan__revision {
    margin: 0;
    color: var(--sw-text-muted);
}

.home-plan__caption {
    margin: 0;
    max-width: 60ch;
    color: var(--sw-text-secondary);
}

.home-plan__sheet {
    margin-top: var(--sw-space-xs);
}

.home-plan__sheet--wide,
.home-plan__sheet--ultra {
    display: none;
}

.home-plan__legend {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs) var(--sw-space-sm);
    margin: 0;
    padding: 0;
    list-style: none;
    color: var(--sw-text-muted);
}

@media (min-width: 720px) {
    .home-plan__sheet--compact {
        display: none;
    }

    .home-plan__sheet--wide {
        display: block;
    }
}

@media (min-width: 1400px) {
    .home-plan__sheet--wide {
        display: none;
    }

    .home-plan__sheet--ultra {
        display: block;
    }
}
</style>
