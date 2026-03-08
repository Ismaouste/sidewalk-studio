<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { ContentItem } from '@/types';

const page = usePage<{ site: { locale: string } }>();

const props = withDefaults(
    defineProps<{
        item: ContentItem;
        compact?: boolean;
    }>(),
    {
        compact: false,
    },
);

const copy = computed(() => {
    const isFrench = page.props.site.locale === 'fr';

    return {
        sectionLabel:
            props.item.section === 'case-studies'
                ? isFrench
                    ? 'Référence'
                    : 'Reference'
                : props.item.category === 'journal'
                  ? 'Journal'
                  : isFrench
                    ? 'Note'
                    : 'Note',
        videoLabel: isFrench ? 'vidéo prête' : 'video ready',
    };
});
</script>

<template>
    <div class="content-visual" :class="{ 'content-visual--compact': compact }">
        <img
            class="content-visual__image"
            :src="props.item.image_url"
            :alt="props.item.image_alt"
            loading="lazy"
        />
        <div class="content-visual__overlay">
            <span class="content-visual__section">
                {{ copy.sectionLabel }}
            </span>
            <span v-if="props.item.featured_video" class="content-visual__badge">
                {{ copy.videoLabel }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.content-visual {
    position: relative;
    overflow: hidden;
    border-radius: calc(var(--sw-radius-lg) - 4px);
    min-height: 12rem;
    background: color-mix(in srgb, var(--sw-bg-grid) 70%, transparent);
}

.content-visual--compact {
    min-height: 9.5rem;
}

.content-visual__image {
    display: block;
    width: 100%;
    height: 100%;
    min-height: inherit;
    object-fit: cover;
}

.content-visual__overlay {
    position: absolute;
    inset: auto 0 0;
    display: flex;
    justify-content: space-between;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-xs);
    background: linear-gradient(
        180deg,
        transparent,
        color-mix(in srgb, var(--sw-bg-base) 88%, transparent)
    );
}

.content-visual__section,
.content-visual__badge {
    font-family: var(--sw-font-code);
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.content-visual__section {
    color: color-mix(in srgb, var(--sw-text-primary) 84%, transparent);
}

.content-visual__badge {
    color: var(--sw-accent-sun);
}
</style>
