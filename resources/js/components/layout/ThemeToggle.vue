<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';

const { currentTheme, setTheme } = useTheme();
</script>

<template>
    <div class="theme-toggle" role="group" aria-label="Theme">
        <button
            type="button"
            class="theme-toggle__option"
            :class="{
                'theme-toggle__option--active': currentTheme === 'morning',
            }"
            :aria-pressed="currentTheme === 'morning'"
            @click="setTheme('morning')"
        >
            Morning
        </button>
        <button
            type="button"
            class="theme-toggle__option"
            :class="{
                'theme-toggle__option--active': currentTheme === 'sunset',
            }"
            :aria-pressed="currentTheme === 'sunset'"
            @click="setTheme('sunset')"
        >
            Sunset
        </button>
    </div>
</template>

<style scoped>
.theme-toggle {
    display: inline-flex;
    align-items: center;
    gap: var(--sw-space-4xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 90%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-surface) 78%, transparent);
    padding: var(--sw-space-4xs);
    backdrop-filter: blur(14px);
}

.theme-toggle__option {
    min-width: 6.75rem;
    min-height: 2.5rem;
    border: 0;
    border-radius: var(--sw-radius-full);
    background: transparent;
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-tab-inactive);
    transition:
        background-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.theme-toggle__option:hover {
    transform: translateY(-1px);
}

.theme-toggle__option--active {
    background: var(--sw-bg-elevated);
    color: var(--sw-tab-active);
    box-shadow: var(--sw-shadow-sm);
}

@media (max-width: 640px) {
    .theme-toggle {
        width: 100%;
    }

    .theme-toggle__option {
        flex: 1;
        min-width: 0;
    }
}
</style>
