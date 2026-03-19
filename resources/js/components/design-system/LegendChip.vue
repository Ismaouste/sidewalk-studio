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
    width: fit-content;
    white-space: nowrap;
    font-family: var(--sw-font-heading);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--chip-accent);
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
