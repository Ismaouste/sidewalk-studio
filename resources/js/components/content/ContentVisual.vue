<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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

const imageLoaded = ref(false);

const copy = computed(() => {
    const isFrench = page.props.site.locale === 'fr';

    return {
        videoLabel: isFrench ? 'vidéo prête' : 'video ready',
    };
});

const isMinimal = computed(
    () => props.item.section === 'writing' && props.item.category !== 'journal',
);

function handleImageLoad(): void {
    imageLoaded.value = true;
}
</script>

<template>
    <div
        class="content-visual"
        :class="{
            'content-visual--compact': compact,
            'content-visual--loaded': imageLoaded,
            'content-visual--minimal': isMinimal,
        }"
    >
        <img
            class="content-visual__image"
            :src="props.item.image_url"
            :alt="props.item.image_alt"
            loading="lazy"
            decoding="async"
            @load="handleImageLoad"
        />
        <div v-if="props.item.featured_video" class="content-visual__overlay">
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
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-lg);
    min-width: 0;
    min-height: 8.25rem;
    background: var(--sw-bg-grid);
}

.content-visual--compact {
    min-height: clamp(5.4rem, 14vw, 7.2rem);
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
    object-fit: cover;
    border-radius: 0;
    opacity: 0;
    filter: blur(18px) saturate(88%);
    transform: scale(1.035);
    transition:
        opacity 220ms ease-out,
        filter 280ms ease-out,
        transform 280ms ease-out;
}

.content-visual--loaded .content-visual__image {
    opacity: 1;
    filter: blur(0) saturate(100%);
    transform: scale(1);
}

.content-visual__image[src$='.svg'] {
    width: 100%;
    height: 100%;
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
