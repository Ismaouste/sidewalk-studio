<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        as?: string;
        tone?: 'surface' | 'grid' | 'elevated';
    }>(),
    {
        as: 'div',
        tone: 'surface',
    },
);
</script>

<template>
    <component :is="props.as" class="panel-frame" :class="`panel-frame--${props.tone}`">
        <slot />
    </component>
</template>

<style scoped>
.panel-frame {
    border: var(--sw-runtime-line-thickness, 1px) solid var(--sw-border);
    border-radius: var(--sw-radius-lg);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

.panel-frame--surface {
    background: var(--sw-bg-surface);
}

.panel-frame--grid {
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, var(--sw-bg-surface));
}

.panel-frame--elevated {
    background: var(--sw-bg-elevated);
}
</style>
