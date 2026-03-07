<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        label: string;
        tone?: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
    }>(),
    {
        tone: 'dominant',
    },
);
</script>

<template>
    <span class="legend-chip" :class="`legend-chip--${props.tone}`">
        {{ props.label }}
    </span>
</template>

<style scoped>
.legend-chip {
    --chip-accent: var(--sw-accent-dominant);
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    border: 1px solid var(--sw-border);
    border-left: 3px solid var(--chip-accent);
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sw-bg-grid) 72%, transparent);
    padding: 0 var(--sw-space-2xs);
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-text-secondary);
}

.legend-chip--green {
    --chip-accent: var(--sw-accent-green);
}

.legend-chip--sun {
    --chip-accent: var(--sw-accent-sun);
}

.legend-chip--coral {
    --chip-accent: var(--sw-accent-coral);
}

.legend-chip--violet {
    --chip-accent: var(--sw-accent-violet);
}
</style>
