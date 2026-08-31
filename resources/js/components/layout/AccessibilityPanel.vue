<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useAccessibilityPreferences } from '@/composables/useAccessibilityPreferences';
import { copy as copyTree } from '@/copy';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const panelId = 'accessibility-preferences';
const isOpen = ref(false);

const {
    motionPreference,
    contrastPreference,
    toggleReducedMotion,
    toggleBoostContrast,
} = useAccessibilityPreferences();

const copy = computed(
    () => copyTree[page.props.site.locale].layout.accessibility,
);

// Opening, closing, click-outside, Escape, the top layer and the scrim are all
// the popover attribute's job. The invoker's expanded state is the one thing
// Chromium does not expose yet, so `toggle` mirrors back what the UA just did.
function handleToggle(event: ToggleEvent): void {
    isOpen.value = event.newState === 'open';
}
</script>

<template>
    <div class="accessibility-panel">
        <button
            type="button"
            class="accessibility-panel__trigger"
            :popovertarget="panelId"
            :aria-expanded="isOpen"
            aria-haspopup="dialog"
            :aria-label="copy.buttonLabel"
        >
            {{ copy.buttonLabel }}
        </button>

        <div
            :id="panelId"
            popover
            class="accessibility-panel__popover"
            role="dialog"
            :aria-label="copy.panelLabel"
            @toggle="handleToggle"
        >
            <button
                type="button"
                class="accessibility-panel__close"
                :popovertarget="panelId"
                popovertargetaction="hide"
                :aria-label="copy.closeLabel"
            >
                ✕
            </button>
            <button
                type="button"
                class="accessibility-panel__option"
                :class="{
                    'accessibility-panel__option--active':
                        motionPreference === 'reduced',
                }"
                :aria-pressed="motionPreference === 'reduced'"
                @click="toggleReducedMotion"
            >
                <span class="accessibility-panel__option-copy">
                    <span class="accessibility-panel__option-title">
                        {{ copy.reducedMotion }}
                    </span>
                    <span class="accessibility-panel__option-hint">
                        {{ copy.reducedMotionHint }}
                    </span>
                </span>
                <span class="accessibility-panel__state">
                    {{
                        motionPreference === 'reduced'
                            ? copy.stateOn
                            : copy.stateOff
                    }}
                </span>
            </button>

            <button
                type="button"
                class="accessibility-panel__option"
                :class="{
                    'accessibility-panel__option--active':
                        contrastPreference === 'boost',
                }"
                :aria-pressed="contrastPreference === 'boost'"
                @click="toggleBoostContrast"
            >
                <span class="accessibility-panel__option-copy">
                    <span class="accessibility-panel__option-title">
                        {{ copy.contrast }}
                    </span>
                    <span class="accessibility-panel__option-hint">
                        {{ copy.contrastHint }}
                    </span>
                </span>
                <span class="accessibility-panel__state">
                    {{
                        contrastPreference === 'boost'
                            ? copy.stateOn
                            : copy.stateOff
                    }}
                </span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.accessibility-panel__trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2rem;
    padding-inline: 0.72rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-md);
    background: transparent;
    font-family: var(--sw-font-body);
    font-size: 12px;
    color: var(--sw-text-secondary);
    transition:
        border-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.accessibility-panel__close {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 1.8rem;
    height: 1.8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: var(--sw-text-muted);
    font-family: var(--sw-font-body);
    font-size: 14px;
    cursor: pointer;
    transition: background-color var(--sw-motion-fast);
}

.accessibility-panel__close:hover,
.accessibility-panel__close:focus-visible {
    background: color-mix(in srgb, var(--sw-border) 60%, transparent);
    color: var(--sw-text-primary);
}

/* The UA gives every [popover] a centred fixed box with a border and a system
   background; each declaration below replaces one of those. Sitting in the top
   layer is what lets the panel drop its z-index and its stacking contract with
   the footer — nothing can paint over it. */
.accessibility-panel__popover {
    position: fixed;
    inset: auto var(--sw-space-xs) var(--sw-space-md) auto;
    width: min(19rem, calc(100vw - 2 * var(--sw-space-xs)));
    max-width: none;
    height: auto;
    max-height: none;
    margin: 0;
    display: grid;
    gap: var(--sw-space-3xs);
    padding: 10px;
    padding-top: 28px;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-elevated) 92%, transparent);
    color: var(--sw-text-primary);
    overflow: visible;
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
    opacity: 0;
    translate: 0 var(--sw-space-4xs);
    transition:
        opacity var(--sw-motion-fast),
        translate var(--sw-motion-fast),
        display var(--sw-motion-fast) allow-discrete,
        overlay var(--sw-motion-fast) allow-discrete;
}

.accessibility-panel__popover:popover-open {
    opacity: 1;
    translate: none;
}

@starting-style {
    .accessibility-panel__popover:popover-open {
        opacity: 0;
        translate: 0 var(--sw-space-4xs);
    }
}

.accessibility-panel__option {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: var(--sw-space-2xs);
    width: 100%;
    padding: 0.8rem 0.85rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 74%, transparent);
    border-radius: 4px;
    background: color-mix(in srgb, var(--sw-bg-surface) 68%, transparent);
    text-align: left;
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.accessibility-panel__option-copy {
    display: grid;
    gap: 4px;
}

.accessibility-panel__option-title {
    font-family: var(--sw-font-body);
    font-size: 13px;
    font-weight: 600;
    color: var(--sw-text-primary);
}

.accessibility-panel__option-hint {
    font-size: 11px;
    line-height: 1.35;
    color: var(--sw-text-secondary);
}

.accessibility-panel__state {
    font-family: var(--sw-font-code);
    font-size: 11px;
    color: var(--sw-accent-dominant);
}

.accessibility-panel__option--active {
    border-color: color-mix(
        in srgb,
        var(--sw-accent-dominant) 26%,
        var(--sw-border)
    );
    background: color-mix(
        in srgb,
        var(--sw-bg-elevated) 88%,
        var(--sw-accent-dominant) 12%
    );
}

@media (max-width: 640px) {
    .accessibility-panel__popover {
        inset: auto var(--sw-space-xs) var(--sw-space-md) var(--sw-space-xs);
        width: auto;
    }

    /* The scrim used to be a full-screen <button> rendered only to catch the
       click that closed the panel. Light dismiss catches that click, so the
       element is gone and the dimming is the top layer's own ::backdrop.
       ::backdrop inherits from its originating element, so the tokens resolve
       here — including --sw-motion-fast, which is what makes the fade honour
       reduced motion without a rule of its own. */
    .accessibility-panel__popover::backdrop {
        background: var(--sw-scrim);
        -webkit-backdrop-filter: var(--sw-scrim-backdrop-filter);
        backdrop-filter: var(--sw-scrim-backdrop-filter);
        opacity: 0;
        transition:
            opacity var(--sw-motion-fast),
            display var(--sw-motion-fast) allow-discrete,
            overlay var(--sw-motion-fast) allow-discrete;
    }

    .accessibility-panel__popover:popover-open::backdrop {
        opacity: 1;
    }

    @starting-style {
        .accessibility-panel__popover:popover-open::backdrop {
            opacity: 0;
        }
    }
}

@media (hover: hover) {
    .accessibility-panel__trigger:hover {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
        color: var(--sw-text-primary);
        background: color-mix(in srgb, var(--sw-bg-surface) 22%, transparent);
    }

    .accessibility-panel__option:hover {
        transform: translateY(-1px);
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
    }
}
</style>
