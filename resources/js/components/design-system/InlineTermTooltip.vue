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
    text-transform: none;
    letter-spacing: normal;
    anchor-name: --inline-term-anchor;
}

.inline-term-tooltip__label {
    color: inherit;
    text-decoration: underline;
    text-decoration-style: dotted;
    text-decoration-thickness: 1px;
    text-underline-offset: 0.18em;
    cursor: help;
    text-transform: none;
    letter-spacing: normal;
}

.inline-term-tooltip__popup {
    position: absolute;
    left: 50%;
    bottom: calc(100% + 10px);
    z-index: 3;
    width: max-content;
    min-width: min(11rem, calc(100vw - 2rem));
    max-width: min(17rem, calc(100vw - 2rem));
    border: 1px solid
        color-mix(in srgb, var(--inline-term-accent) 22%, var(--sw-border));
    border-radius: 6px;
    background: color-mix(
        in srgb,
        var(--sw-bg-elevated) 56%,
        var(--inline-term-accent) 4%
    );
    -webkit-backdrop-filter: blur(14px) saturate(132%);
    backdrop-filter: blur(14px) saturate(132%);
    box-shadow:
        0 12px 32px -16px color-mix(in srgb, black 36%, transparent),
        0 0 0 1px color-mix(in srgb, white 6%, transparent) inset;
    padding: 0.6rem 0.8rem;
    color: var(--sw-text-primary);
    font-family: var(--sw-font-body);
    font-size: 0.78rem;
    font-weight: 500;
    line-height: 1.45;
    letter-spacing: normal;
    text-transform: none;
    text-align: left;
    white-space: normal;
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, 4px);
    transition:
        opacity var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.inline-term-tooltip__popup::after {
    content: '';
    position: absolute;
    left: calc(50% - 5px);
    top: calc(100% - 1px);
    width: 10px;
    height: 10px;
    border-right: 1px solid
        color-mix(in srgb, var(--inline-term-accent) 22%, var(--sw-border));
    border-bottom: 1px solid
        color-mix(in srgb, var(--inline-term-accent) 22%, var(--sw-border));
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
    pointer-events: auto;
    transform: translate(-50%, 0);
}

@supports (anchor-name: --x) {
    .inline-term-tooltip__popup {
        position: fixed;
        inset: auto;
        position-anchor: --inline-term-anchor;
        bottom: anchor(top);
        left: anchor(center);
        translate: -50% calc(-10px - 4px);
        margin: 0;
        width: max-content;
        max-width: min(17rem, calc(100dvw - 2 * var(--sw-space-xs)));
        transform: none;
        position-try-fallbacks:
            --inline-term-shift-end, --inline-term-shift-start,
            --inline-term-flip-bottom;
    }

    .inline-term-tooltip:hover .inline-term-tooltip__popup,
    .inline-term-tooltip:focus-within .inline-term-tooltip__popup,
    .inline-term-tooltip:focus .inline-term-tooltip__popup {
        translate: -50% -10px;
        transform: none;
    }
}

@position-try --inline-term-shift-end {
    left: auto;
    right: var(--sw-space-xs);
    bottom: anchor(top);
    translate: 0 -10px;
}

@position-try --inline-term-shift-start {
    left: var(--sw-space-xs);
    right: auto;
    bottom: anchor(top);
    translate: 0 -10px;
}

@position-try --inline-term-flip-bottom {
    bottom: auto;
    top: anchor(bottom);
    left: anchor(center);
    translate: -50% 10px;
}

@media (prefers-reduced-motion: reduce) {
    .inline-term-tooltip__popup {
        transition: none;
    }
}

@media (max-width: 640px) {
    .inline-term-tooltip__popup {
        font-size: 0.76rem;
        padding: 0.55rem 0.75rem;
    }

    .inline-term-tooltip__popup::after {
        display: none;
    }
}
</style>
