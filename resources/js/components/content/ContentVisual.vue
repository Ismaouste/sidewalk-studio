<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { rng } from '@/components/stage/stains';
import { copy as copyTree } from '@/copy';
import type { ContentItem, SiteProps } from '@/types';

const props = withDefaults(
    defineProps<{
        item: ContentItem;
        compact?: boolean;
    }>(),
    {
        compact: false,
    },
);

const page = usePage<{ site: SiteProps }>();

const imageLoaded = ref(false);

const copy = computed(
    () => copyTree[page.props.site.locale].content.contentVisual,
);

const isMinimal = computed(
    () => props.item.section === 'writing' && props.item.category !== 'journal',
);
const isPlaceholder = computed(() => props.item.image.kind === 'placeholder');

function handleImageLoad(): void {
    imageLoaded.value = true;
}

// The placeholder is a plate drawn here, not an image from the server: it reads
// the theme tokens, so it follows the light and the night theme. Case studies
// take the path blue, the journal stays in ink.
const isCase = computed(() => props.item.section === 'case-studies');
const plateLabel = computed(() =>
    isCase.value ? copy.value.caseLabel : copy.value.journalLabel,
);
const plateDate = computed(() => props.item.published_at?.slice(0, 7) ?? '');

function seedOf(text: string): number {
    let h = 2166136261;

    for (let i = 0; i < text.length; i += 1) {
        h = Math.imul(h ^ text.charCodeAt(i), 16777619);
    }

    return h >>> 0;
}

/** A small abstract drawing of lots and interfaces, the same for a given slug. */
const drawing = computed(() => {
    const random = rng(seedOf(props.item.slug));
    const count = 3 + Math.floor(random() * 3);
    const nodes = Array.from({ length: count }, (_, i) => ({
        x: 14 + (i * 172) / (count - 1) + (random() - 0.5) * 14,
        y: 14 + random() * 52,
    }));
    const edges = nodes.slice(0, -1).map((a, i) => {
        const b = nodes[i + 1] ?? a;

        return `M${(a.x + 8).toFixed(1)} ${(a.y + 4).toFixed(1)}L${(b.x - 2).toFixed(1)} ${(b.y + 4).toFixed(1)}`;
    });

    return { nodes, edges };
});
</script>

<template>
    <div
        class="content-visual"
        :class="{
            'content-visual--compact': compact,
            'content-visual--loaded': imageLoaded,
            'content-visual--minimal': isMinimal,
            'content-visual--placeholder': isPlaceholder,
        }"
    >
        <div
            v-if="isPlaceholder"
            class="content-visual__plate"
            :class="{ 'content-visual__plate--case': isCase }"
            role="img"
            :aria-label="props.item.image_alt || props.item.title"
        >
            <p class="content-visual__plate-meta">
                <span>{{ plateLabel }}</span>
                <span v-if="plateDate">{{ plateDate }}</span>
            </p>
            <svg
                class="content-visual__plate-drawing"
                viewBox="0 0 200 80"
                aria-hidden="true"
                focusable="false"
            >
                <path
                    v-for="(edge, i) in drawing.edges"
                    :key="`e${i}`"
                    class="content-visual__plate-edge"
                    :d="edge"
                />
                <rect
                    v-for="(node, i) in drawing.nodes"
                    :key="`n${i}`"
                    class="content-visual__plate-node"
                    :class="{
                        'content-visual__plate-node--here':
                            i === drawing.nodes.length - 1,
                    }"
                    :x="node.x"
                    :y="node.y"
                    width="10"
                    height="8"
                />
            </svg>
        </div>
        <img
            v-else
            class="content-visual__image"
            :src="props.item.image_url"
            :alt="props.item.image_alt"
            loading="lazy"
            decoding="async"
            @load="handleImageLoad"
        />
        <div v-if="props.item.featured_video" class="content-visual__overlay">
            <span
                v-if="props.item.featured_video"
                class="content-visual__badge"
            >
                {{ copy.videoLabel }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.content-visual {
    position: relative;
    overflow: hidden;
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-lg);
    width: 100%;
    max-width: 100%;
    min-width: 0;
    min-height: 8.25rem;
    background: var(--sw-bg-grid);
}

.content-visual--placeholder {
    aspect-ratio: 16 / 10;
    min-height: clamp(11rem, 32vw, 18rem);
    max-height: clamp(18rem, 36vw, 26rem);
}

.content-visual--compact {
    min-height: clamp(5.4rem, 14vw, 7.2rem);
}

.content-visual--compact.content-visual--placeholder {
    aspect-ratio: 3 / 2;
    min-height: 0;
}

.content-visual--minimal {
    min-height: 5.25rem;
}

.content-visual__image {
    display: block;
    width: 100%;
    height: 100%;
    min-height: 100%;
    max-height: 100%;
    max-width: 100%;
    box-sizing: border-box;
    object-fit: cover;
    border-radius: 0;
    opacity: 0;
    transform: scale(1.035);
    transition:
        opacity 220ms ease-out,
        filter 280ms ease-out,
        transform 280ms ease-out;
}

.content-visual--loaded .content-visual__image {
    opacity: 1;
    transform: scale(1);
}

.content-visual__image[src$='.svg'] {
    width: 100%;
    height: 100%;
    padding: clamp(0.35rem, 0.8vw, 0.65rem);
    object-fit: contain;
    transform: scale(1);
}

.content-visual--placeholder .content-visual__image[src$='.svg'] {
    padding: 0;
    object-fit: cover;
}

.content-visual--compact .content-visual__image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    min-height: 0;
    max-height: none;
}

.content-visual--compact .content-visual__image[src$='.svg'] {
    padding: clamp(0.25rem, 0.6vw, 0.45rem);
}

@media (max-width: 640px) {
    .content-visual--placeholder {
        aspect-ratio: 14 / 11;
        min-height: 0;
        max-height: none;
    }

    .content-visual--placeholder:not(.content-visual--compact) {
        display: none;
    }

    .content-visual--compact.content-visual--placeholder {
        aspect-ratio: 4 / 3;
    }

    .content-visual--compact .content-visual__image[src$='.svg'] {
        width: 100%;
        height: 100%;
        padding: 0;
        object-fit: cover;
    }
}

@media (min-width: 1040px) {
    .content-visual--placeholder {
        aspect-ratio: 16 / 7;
        max-height: 22rem;
    }
}

/* The plate: paper ground, a hairline under the label, a small drawing of lots. */
.content-visual__plate {
    --plate-accent: var(--sw-text-primary);

    display: grid;
    grid-template-rows: auto 1fr;
    gap: var(--sw-space-2xs);
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    padding: var(--sw-space-xs);
    background: var(--sw-bg-surface);
    color: var(--sw-text-primary);
}

.content-visual__plate--case {
    --plate-accent: var(--sw-path);
}

.content-visual__plate-meta {
    display: flex;
    justify-content: space-between;
    gap: var(--sw-space-xs);
    margin: 0;
    padding-bottom: var(--sw-space-3xs);
    border-bottom: var(--sw-hairline) solid var(--sw-border);
    color: var(--sw-text-muted);
    font-family: var(--sw-font-code);
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.content-visual__plate-drawing {
    align-self: center;
    justify-self: center;
    width: min(100%, 24rem);
    height: auto;
    overflow: visible;
}

.content-visual__plate-edge {
    fill: none;
    stroke: var(--plate-accent);
    stroke-width: 1;
    stroke-dasharray: 3 3;
}

.content-visual__plate-node {
    fill: var(--sw-bg-surface);
    stroke: var(--sw-text-primary);
    stroke-width: 1;
}

.content-visual__plate-node--here {
    fill: var(--plate-accent);
    stroke: var(--plate-accent);
}

.content-visual__plate-meta span {
    white-space: nowrap;
}

/* In a small plate only the label fits; the date is on the card. */
.content-visual--compact .content-visual__plate-meta span:nth-child(2) {
    display: none;
}

.content-visual--compact .content-visual__plate {
    padding: var(--sw-space-2xs);
}

.content-visual__overlay {
    position: absolute;
    inset: auto 0 0;
    display: flex;
    justify-content: space-between;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-xs);
    background: color-mix(in srgb, var(--sw-bg-base) 64%, transparent);
}

.content-visual__badge {
    font-family: var(--sw-font-code);
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.content-visual__badge {
    color: var(--sw-accent-sun);
}
</style>
