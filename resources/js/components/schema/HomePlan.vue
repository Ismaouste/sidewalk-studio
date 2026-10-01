<script setup lang="ts">
/**
 * The timeline on the home page: the projects and roles on a time axis, the
 * technologies and notions they used, and the pages and publications that tell
 * them. A drawing from 900 px (cards on pointing, on focus and on tapping), the
 * same content as a list below that. The legend says what each colour and each
 * pictogram means: a page of the site, an external link, and one pictogram per
 * kind of publication (article, note, case study).
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { copy as copyTree } from '@/copy';
import { localizePublicHref } from '@/lib/publicHref';
import type { SiteProps } from '@/types';
import { KIND_ICONS } from './schema';
import Schema from './Schema.vue';
import { buildTimeline } from './timelineLayout';
import type { Publication } from './timelineLayout';

const props = defineProps<{ publications: Publication[] }>();

defineOptions({ name: 'HomePlan' });

const page = usePage<{ site: SiteProps }>();
const locale = computed(() => page.props.site.locale);
const copy = computed(() => copyTree[locale.value].pages.timeline);
const localize = (href: string) => localizePublicHref(href, locale.value);
const timeline = computed(() =>
    buildTimeline(copy.value, props.publications, localize),
);

const legend = computed(() => [
    { key: 'page', label: copy.value.legend.page },
    { key: 'external', label: copy.value.legend.external },
    { key: 'article', label: copy.value.legend.article },
    { key: 'note', label: copy.value.legend.note },
    { key: 'case', label: copy.value.legend.case },
    { key: 'notion', label: copy.value.legend.notion },
]);
const isExternal = (href: string) => /^https?:\/\//.test(href);
</script>

<template>
    <section class="sw-section home-plan" aria-labelledby="home-plan-title">
        <p class="type-meta home-plan__revision">{{ copy.revision }}</p>
        <h2 id="home-plan-title" class="type-h2">{{ copy.title }}</h2>
        <p class="type-body home-plan__caption">{{ copy.caption }}</p>

        <ul class="type-meta home-plan__legend" :aria-label="copy.legend.hint">
            <li v-for="item in legend" :key="item.key">
                <span
                    class="home-plan__swatch"
                    :class="`home-plan__swatch--${item.key}`"
                    aria-hidden="true"
                >
                    <svg
                        v-if="KIND_ICONS[item.key as keyof typeof KIND_ICONS]"
                        viewBox="0 0 12 12"
                        focusable="false"
                    >
                        <path
                            :d="KIND_ICONS[item.key as keyof typeof KIND_ICONS]"
                        />
                    </svg>
                </span>
                {{ item.label }}
            </li>
            <li class="home-plan__hint">{{ copy.legend.hint }}</li>
        </ul>

        <Schema class="home-plan__sheet" :data="timeline.schema" />

        <div class="home-plan__list">
            <h3 class="type-h3">{{ copy.listTitle }}</h3>
            <ol>
                <li v-for="entry in timeline.entries" :key="entry.id">
                    <p class="home-plan__entry-head">
                        <component
                            :is="
                                entry.href && isExternal(entry.href)
                                    ? 'a'
                                    : 'span'
                            "
                            class="home-plan__entry-title"
                            v-bind="
                                entry.href && isExternal(entry.href)
                                    ? {
                                          href: entry.href,
                                          target: '_blank',
                                          rel: entry.nofollow
                                              ? 'nofollow noopener noreferrer'
                                              : 'noopener',
                                      }
                                    : {}
                            "
                            >{{ entry.label }}</component
                        >
                        <span v-if="entry.period" class="type-meta">{{
                            entry.period
                        }}</span>
                    </p>
                    <p class="type-body-sm home-plan__entry-note">
                        {{ entry.note }}
                    </p>
                    <p v-if="entry.notions.length" class="type-meta">
                        {{ copy.notionsLabel }}:
                        {{ entry.notions.map((n) => n.label).join(' · ') }}
                    </p>
                    <ul v-if="entry.related.length" class="home-plan__related">
                        <li v-for="item in entry.related" :key="item.href">
                            <a :href="item.href">
                                <svg viewBox="0 0 12 12" aria-hidden="true">
                                    <path :d="KIND_ICONS[item.kind] ?? ''" />
                                </svg>
                                {{ item.label }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ol>
        </div>
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

.home-plan__legend {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--sw-space-3xs) var(--sw-space-sm);
    margin: 0;
    padding: 0;
    list-style: none;
    color: var(--sw-text-muted);
}

.home-plan__legend li {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
}

.home-plan__hint {
    display: none;
    margin-left: auto;
}

/* The legend swatches draw the boxes of the plan in small */
.home-plan__swatch {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: 1.5rem;
    height: 0.95rem;
    border: var(--sw-hairline) solid var(--sw-text-primary);
    background: var(--sw-bg-surface);
}

.home-plan__swatch svg {
    width: 0.7rem;
    height: 0.7rem;
    fill: none;
    stroke: var(--sw-ink);
    stroke-width: 1.2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.home-plan__swatch--external {
    border-color: var(--sw-path);
}

.home-plan__swatch--external svg {
    stroke: var(--sw-path);
}

.home-plan__swatch--article,
.home-plan__swatch--note,
.home-plan__swatch--case {
    background: var(--sw-mark);
}

.home-plan__swatch--notion {
    border-style: dashed;
}

.home-plan__sheet {
    display: none;
    margin-top: var(--sw-space-xs);
}

.home-plan__list {
    display: grid;
    gap: var(--sw-space-xs);
}

.home-plan__list h3 {
    margin: 0;
}

.home-plan__list ol {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.home-plan__list li {
    display: grid;
    gap: 0.25rem;
}

.home-plan__list > ol > li {
    padding: var(--sw-space-xs);
    border: var(--sw-hairline) solid var(--sw-text-primary);
    background: var(--sw-bg-surface);
}

.home-plan__list p {
    margin: 0;
}

.home-plan__entry-head {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 0 0.75rem;
}

.home-plan__entry-title {
    font-weight: 500;
}

a.home-plan__entry-title {
    color: var(--sw-path);
}

.home-plan__entry-note {
    color: var(--sw-text-secondary);
}

.home-plan__related {
    display: grid;
    gap: 0.2rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.home-plan__related a {
    display: inline-flex;
    align-items: flex-start;
    gap: 0.45rem;
    color: var(--sw-text-primary);
}

.home-plan__related svg {
    flex: none;
    box-sizing: content-box;
    width: 0.8rem;
    height: 0.8rem;
    margin-top: 0.15rem;
    padding: 1px;
    background: var(--sw-mark);
    fill: none;
    stroke: var(--sw-ink);
    stroke-width: 1.15;
    stroke-linecap: round;
    stroke-linejoin: round;
}

@media (min-width: 900px) {
    .home-plan__hint {
        display: inline-flex;
    }

    .home-plan__sheet {
        display: block;
    }

    /* The drawing carries its own text equivalent: the list is for narrower screens */
    .home-plan__list {
        display: none;
    }
}
</style>
