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
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-surface);
    padding: var(--sw-space-3xs);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

.media-embed__frame {
    min-height: 18rem;
    overflow: hidden;
    border-radius: 4px;
    background: color-mix(in srgb, var(--sw-bg-grid) 76%, transparent);
}
</style>
