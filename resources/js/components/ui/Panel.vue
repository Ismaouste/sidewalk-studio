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
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-lg);
    box-shadow: var(--sw-shadow-sm);
    backdrop-filter: blur(18px);
}

.panel-frame--surface {
    background: color-mix(in srgb, var(--sw-bg-surface) 82%, transparent);
}

.panel-frame--grid {
    background: color-mix(in srgb, var(--sw-bg-grid) 76%, transparent);
}

.panel-frame--elevated {
    background: color-mix(in srgb, var(--sw-bg-elevated) 82%, transparent);
    box-shadow: var(--sw-shadow-md);
}
</style>
