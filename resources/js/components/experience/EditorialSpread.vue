<script setup lang="ts">
defineProps<{
    id: string;
    eyebrow: string;
    title: string;
    summary: string;
    paragraphs: string[];
    pills?: string[];
    items?: string[];
    railLabel?: string;
    /**
     * A marginal note beside the spread.
     *
     * `prompt` is the question that produced the quote, and it is what turns
     * a pull quote into a Q&A: the caption reads as the thing that was asked
     * rather than as an attribution, so it takes no em dash. `author` is the
     * older shape and still renders with one.
     */
    marginalia?: { quote: string; author?: string; prompt?: string };
}>();
</script>

<template>
    <article :id="id" class="editorial-spread">
        <header class="editorial-spread__head">
            <p class="type-eyebrow editorial-spread__eyebrow">{{ eyebrow }}</p>
            <h2 class="type-h1 editorial-spread__title">{{ title }}</h2>
            <p class="type-body-lg editorial-spread__summary">{{ summary }}</p>
        </header>
        <div class="editorial-spread__body">
            <div class="editorial-spread__prose">
                <p
                    v-for="paragraph in paragraphs"
                    :key="paragraph"
                    class="type-body editorial-spread__paragraph"
                >
                    {{ paragraph }}
                </p>
            </div>
            <aside class="editorial-spread__rail">
                <p
                    v-if="railLabel"
                    class="type-eyebrow editorial-spread__rail-label"
                >
                    {{ railLabel }}
                </p>
                <div v-if="pills?.length" class="editorial-spread__pills">
                    <span
                        v-for="pill in pills"
                        :key="pill"
                        class="editorial-spread__pill"
                    >
                        {{ pill }}
                    </span>
                </div>
                <ul v-if="items?.length" class="editorial-spread__items">
                    <li
                        v-for="item in items"
                        :key="item"
                        class="editorial-spread__item"
                    >
                        {{ item }}
                    </li>
                </ul>
                <figure v-if="marginalia" class="editorial-spread__marginalia">
                    <blockquote class="editorial-spread__quote">
                        {{ marginalia.quote }}
                    </blockquote>
                    <figcaption class="editorial-spread__author">
                        <template v-if="marginalia.prompt">{{
                            marginalia.prompt
                        }}</template>
                        <template v-else-if="marginalia.author"
                            >— {{ marginalia.author }}</template
                        >
                    </figcaption>
                </figure>
            </aside>
        </div>
    </article>
</template>

<style scoped>
.editorial-spread {
    display: grid;
    gap: var(--sw-space-sm);
    scroll-margin-top: calc(var(--sw-header-offset) + var(--sw-space-sm));
    min-width: 0;
}

.editorial-spread__head {
    display: grid;
    gap: var(--sw-space-3xs);
    max-width: 56rem;
}

.editorial-spread__eyebrow {
    color: var(--sw-accent-violet);
}

.editorial-spread__title {
    margin: 0;
    color: var(--sw-text-primary);
}

.editorial-spread__summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
    max-width: 52rem;
}

.editorial-spread__body {
    display: grid;
    gap: var(--sw-space-md);
    min-width: 0;
}

@media (min-width: 920px) {
    .editorial-spread__body {
        grid-template-columns: minmax(0, 1.6fr) minmax(14rem, 1fr);
    }
}

.editorial-spread__prose {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.editorial-spread__paragraph {
    margin: 0;
    max-width: 62ch;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.editorial-spread__rail {
    display: grid;
    gap: var(--sw-space-sm);
    min-width: 0;
    padding: var(--sw-space-sm);
    border-left: 1px solid color-mix(in srgb, var(--sw-border) 70%, transparent);
}

@media (max-width: 919px) {
    .editorial-spread__rail {
        border-left: none;
        border-top: 1px solid
            color-mix(in srgb, var(--sw-border) 70%, transparent);
        padding: var(--sw-space-sm) 0 0;
    }
}

.editorial-spread__rail-label {
    margin: 0;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 84%,
        var(--sw-text-primary)
    );
}

.editorial-spread__pills {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.editorial-spread__pill {
    font-family: var(--sw-font-body);
    font-size: 0.78rem;
    font-weight: 600;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 70%,
        var(--sw-text-secondary)
    );
}

.editorial-spread__items {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.editorial-spread__item {
    margin: 0;
    font-size: 0.92rem;
    line-height: 1.55;
    color: var(--sw-text-secondary);
}

.editorial-spread__marginalia {
    margin: 0;
    padding: var(--sw-space-xs) 0 0;
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 60%, transparent);
}

.editorial-spread__quote {
    margin: 0;
    font-family: var(--sw-font-display);
    font-style: italic;
    font-size: 1.05rem;
    line-height: 1.4;
    color: var(--sw-text-primary);
}

.editorial-spread__author {
    margin: var(--sw-space-3xs) 0 0;
    font-family: var(--sw-font-heading);
    font-size: 0.7rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

@supports (animation-timeline: view()) {
    @media (prefers-reduced-motion: no-preference) {
        .editorial-spread__head {
            animation: editorial-spread-reveal linear both;
            animation-timeline: view();
            animation-range: entry 10% cover 30%;
        }
    }
}

@keyframes editorial-spread-reveal {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
