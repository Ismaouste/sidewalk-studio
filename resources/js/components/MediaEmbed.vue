<script setup lang="ts">
const props = defineProps<{
    title: string;
    service: string;
    id: string;
}>();
</script>

<template>
    <div class="media-embed">
        <div
            class="media-embed__frame"
            :data-service="props.service"
            :data-id="props.id"
            :data-title="props.title"
        />
    </div>
</template>

<style scoped>
.media-embed {
    border: 1px solid var(--sw-border);
    border-radius: calc(var(--sw-radius-lg) + 0.5rem);
    background: var(--sw-bg-elevated);
    padding: var(--sw-space-3xs);
    box-shadow: var(--sw-shadow-md);
}

.media-embed__frame {
    min-height: 18rem;
    overflow: hidden;
    border-radius: calc(var(--sw-radius-lg) - 0.25rem);
    background: color-mix(in srgb, var(--sw-bg-grid) 76%, transparent);
}
</style>
