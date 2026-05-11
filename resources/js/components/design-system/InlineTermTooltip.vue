<script setup lang="ts">
import { ref } from 'vue';

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

const triggerRef = ref<HTMLElement | null>(null);
const popupRef = ref<HTMLElement | null>(null);

function positionPopup(): void {
    const trigger = triggerRef.value;
    const popup = popupRef.value;
    if (!trigger || !popup) return;

    const t = trigger.getBoundingClientRect();
    const margin = 8;

    popup.style.visibility = 'hidden';
    popup.style.opacity = '1';
    popup.style.left = '0';
    popup.style.top = '0';
    const popupWidth = popup.offsetWidth;
    const popupHeight = popup.offsetHeight;
    popup.style.visibility = '';
    popup.style.opacity = '';

    const wantedLeft = t.left + t.width / 2 - popupWidth / 2;
    const left = Math.max(
        margin,
        Math.min(wantedLeft, window.innerWidth - popupWidth - margin),
    );

    const aboveTop = t.top - popupHeight - 10;
    const top = aboveTop >= margin ? aboveTop : t.bottom + 10;

    const arrowX = t.left + t.width / 2 - left;
    popup.style.left = `${left}px`;
    popup.style.top = `${top}px`;
    popup.style.setProperty('--inline-term-arrow-x', `${arrowX}px`);
    popup.style.setProperty(
        '--inline-term-arrow-side',
        aboveTop >= margin ? 'bottom' : 'top',
    );
}
</script>

<template>
    <span
        ref="triggerRef"
        class="inline-term-tooltip"
        :class="`inline-term-tooltip--${props.tone}`"
        tabindex="0"
        @mouseenter="positionPopup"
        @focus="positionPopup"
        @touchstart.passive="positionPopup"
    >
        <span class="inline-term-tooltip__label">{{ props.label }}</span>
        <span ref="popupRef" class="inline-term-tooltip__popup" role="note">
            {{ props.definition }}
        </span>
    </span>
</template>

<style scoped>
.inline-term-tooltip {
    --inline-term-accent: var(--sw-accent-dominant);
    --inline-term-arrow-x: 50%;
    --inline-term-arrow-side: bottom;
    position: relative;
    display: inline-flex;
    align-items: baseline;
    outline: none;
    text-transform: none;
    letter-spacing: normal;
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
    position: fixed;
    left: 0;
    top: 0;
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
    transform: translateY(4px);
    transition:
        opacity var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.inline-term-tooltip__popup::after {
    content: '';
    position: absolute;
    left: calc(var(--inline-term-arrow-x) - 5px);
    width: 10px;
    height: 10px;
    background: inherit;
    transform: rotate(45deg);
}

.inline-term-tooltip__popup:where([style*='--inline-term-arrow-side: bottom']),
.inline-term-tooltip__popup {
    /* default: arrow on bottom edge of popup (popup is above trigger) */
}

.inline-term-tooltip__popup::after {
    top: calc(100% - 6px);
    border-right: 1px solid
        color-mix(in srgb, var(--inline-term-accent) 22%, var(--sw-border));
    border-bottom: 1px solid
        color-mix(in srgb, var(--inline-term-accent) 22%, var(--sw-border));
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
    transform: translateY(0);
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
}
</style>
