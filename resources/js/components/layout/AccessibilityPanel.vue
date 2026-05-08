<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useAccessibilityPreferences } from '@/composables/useAccessibilityPreferences';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const isOpen = ref(false);
const rootRef = ref<HTMLElement | null>(null);

const { motionPreference, toggleReducedMotion } = useAccessibilityPreferences();

const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              buttonLabel: 'Accessibilité',
              panelLabel: "Réglages d'accessibilité",
              reducedMotion: 'Animations réduites',
              reducedMotionHint:
                  'Fond ambient, loader et transitions plus sobres.',
              contrast: 'Contraste renforcé',
              contrastHint:
                  'Prévu ensuite. Le mode est affiché mais pas encore disponible.',
              unavailable: 'Bientôt',
          }
        : {
              buttonLabel: 'Accessibility',
              panelLabel: 'Accessibility settings',
              reducedMotion: 'Reduced motion',
              reducedMotionHint:
                  'Ambient background, loader, and transitions become quieter.',
              contrast: 'Boost contrast',
              contrastHint:
                  'Planned next. The control is visible but not available yet.',
              unavailable: 'Soon',
          },
);

function togglePanel(): void {
    isOpen.value = !isOpen.value;
}

function closePanel(): void {
    isOpen.value = false;
}

function handleDocumentClick(event: MouseEvent): void {
    if (!isOpen.value || !rootRef.value) {
        return;
    }

    if (!rootRef.value.contains(event.target as Node)) {
        closePanel();
    }
}

function handleEscape(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closePanel();
    }
}

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <div ref="rootRef" class="accessibility-panel">
        <button
            type="button"
            class="accessibility-panel__trigger"
            :aria-expanded="isOpen"
            :aria-haspopup="'dialog'"
            :aria-label="copy.buttonLabel"
            @click="togglePanel"
        >
            {{ copy.buttonLabel }}
        </button>

        <div
            v-if="isOpen"
            class="accessibility-panel__popover"
            role="dialog"
            :aria-label="copy.panelLabel"
        >
            <button
                type="button"
                class="accessibility-panel__option"
                :class="{
                    'accessibility-panel__option--active':
                        motionPreference === 'reduced',
                }"
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
                    {{ motionPreference === 'reduced' ? 'On' : 'Off' }}
                </span>
            </button>

            <button
                type="button"
                class="accessibility-panel__option accessibility-panel__option--disabled"
                disabled
                aria-disabled="true"
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
                    {{ copy.unavailable }}
                </span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.accessibility-panel {
    position: relative;
}

.accessibility-panel__trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2rem;
    padding-inline: 0.72rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: 3px;
    background: transparent;
    font-family: var(--sw-font-body);
    font-size: 12px;
    color: var(--sw-text-secondary);
    transition:
        border-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.accessibility-panel__popover {
    position: absolute;
    right: 0;
    bottom: calc(100% + 10px);
    z-index: 5;
    display: grid;
    gap: 8px;
    width: min(19rem, calc(100vw - 2rem));
    padding: 10px;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-elevated) 92%, transparent);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

@media (max-width: 640px) {
    .accessibility-panel__popover {
        position: fixed;
        right: var(--sw-space-xs);
        left: var(--sw-space-xs);
        bottom: var(--sw-space-md);
        width: auto;
        max-width: calc(100vw - 2 * var(--sw-space-xs));
    }
}

.accessibility-panel__option {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 12px;
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

.accessibility-panel__option--disabled {
    opacity: 0.58;
    cursor: not-allowed;
}

.accessibility-panel__option--disabled .accessibility-panel__option-title,
.accessibility-panel__option--disabled .accessibility-panel__option-hint,
.accessibility-panel__option--disabled .accessibility-panel__state {
    color: color-mix(in srgb, var(--sw-text-muted) 88%, transparent);
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

    .accessibility-panel__option--disabled:hover {
        transform: none;
        border-color: color-mix(in srgb, var(--sw-border) 74%, transparent);
    }
}
</style>
