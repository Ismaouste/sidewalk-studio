<script setup lang="ts">
defineProps<{
    text: string;
    type: 'message' | 'quote';
    author?: string | null;
    compact?: boolean;
}>();
</script>

<template>
    <div class="quote-preview" :class="{ 'quote-preview--compact': compact }">
        <strong
            class="quote-preview__text"
            :class="{ 'quote-preview__text--quote': type === 'quote' }"
        >
            {{ text }}
        </strong>
        <cite v-if="author" class="quote-preview__author">
            {{ author }}
        </cite>
    </div>
</template>

<style scoped>
.quote-preview {
    display: grid;
    gap: 0.35rem;
}

.quote-preview__text {
    font-size: 1rem;
    font-weight: 500;
    line-height: 1.35;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 88%,
        var(--sw-text-primary)
    );
}

.quote-preview__text--quote {
    font-style: italic;
}

.quote-preview__author {
    font-size: 0.72rem;
    font-style: normal;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sw-text-secondary);
}

.quote-preview--compact .quote-preview__text {
    font-size: 0.9rem;
}
</style>
