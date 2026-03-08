<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        label: string;
        definition: string;
        tone?: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
    }>(),
    {
        tone: 'dominant',
    },
);
</script>

<template>
    <span
        class="inline-term-tooltip"
        :class="`inline-term-tooltip--${props.tone}`"
        tabindex="0"
    >
        <span class="inline-term-tooltip__label">{{ props.label }}</span>
        <span class="inline-term-tooltip__popup" role="note">
            {{ props.definition }}
        </span>
    </span>
</template>

<style scoped>
.inline-term-tooltip {
    --inline-term-accent: var(--sw-accent-dominant);
    position: relative;
    display: inline-flex;
    align-items: baseline;
    outline: none;
}

.inline-term-tooltip__label {
    color: inherit;
    text-decoration: underline;
    text-decoration-style: dotted;
    text-decoration-thickness: 1px;
    text-underline-offset: 0.18em;
    cursor: help;
}

.inline-term-tooltip__popup {
    position: absolute;
    left: 0;
    bottom: calc(100% + 10px);
    z-index: 3;
    min-width: max-content;
    max-width: min(20rem, 82vw);
    border: 1px solid color-mix(in srgb, var(--inline-term-accent) 24%, var(--sw-border));
    border-radius: calc(var(--sw-radius-md) + 2px);
    background: color-mix(in srgb, var(--sw-bg-elevated) 90%, var(--inline-term-accent) 10%);
    padding: 0.52rem 0.68rem;
    color: var(--sw-text-primary);
    font-family: var(--sw-font-body);
    font-size: 0.79rem;
    font-weight: 500;
    line-height: 1.35;
    white-space: normal;
    box-shadow: var(--sw-shadow-md);
    opacity: 0;
    pointer-events: none;
    transform: translateY(4px);
    transition:
        opacity var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.inline-term-tooltip__popup::after {
    content: '';
    position: absolute;
    left: 16px;
    top: calc(100% - 1px);
    width: 10px;
    height: 10px;
    border-right: 1px solid color-mix(in srgb, var(--inline-term-accent) 24%, var(--sw-border));
    border-bottom: 1px solid color-mix(in srgb, var(--inline-term-accent) 24%, var(--sw-border));
    transform: rotate(45deg);
    background: inherit;
}

.inline-term-tooltip--green {
    --inline-term-accent: var(--sw-accent-green);
}

.inline-term-tooltip--sun {
    --inline-term-accent: var(--sw-accent-sun);
}

.inline-term-tooltip--coral {
    --inline-term-accent: var(--sw-accent-coral);
}

.inline-term-tooltip--violet {
    --inline-term-accent: var(--sw-accent-violet);
}

.inline-term-tooltip:hover .inline-term-tooltip__popup,
.inline-term-tooltip:focus-within .inline-term-tooltip__popup,
.inline-term-tooltip:focus .inline-term-tooltip__popup {
    opacity: 1;
    transform: translateY(0);
}
</style>
