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
}

.panel-frame--surface {
    background: var(--sw-bg-surface);
}

.panel-frame--grid {
    background: var(--sw-bg-grid);
}

.panel-frame--elevated {
    background: var(--sw-bg-elevated);
    box-shadow: var(--sw-shadow-md);
}
</style>
