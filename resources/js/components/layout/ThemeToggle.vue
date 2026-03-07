<script setup lang="ts">
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';

const { currentTheme, setTheme } = useTheme();

const options = [
    { label: 'Morning', value: 'morning' },
    { label: 'Sunset', value: 'sunset' },
] as const;

const activeIndex = computed(() =>
    Math.max(
        options.findIndex((option) => option.value === currentTheme.value),
        0,
    ),
);

function selectTheme(theme: 'morning' | 'sunset'): void {
    if (currentTheme.value === theme) {
        return;
    }

    setTheme(theme);
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
        return;
    }

    event.preventDefault();
    selectTheme(event.key === 'ArrowLeft' ? 'morning' : 'sunset');
}
</script>

<template>
    <div
        class="theme-toggle"
        role="radiogroup"
        aria-label="Color theme"
        @keydown="handleKeydown"
    >
        <span
            class="theme-toggle__thumb"
            :style="{ transform: `translateX(${activeIndex * 100}%)` }"
            aria-hidden="true"
        />
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            class="theme-toggle__option"
            :class="{
                'theme-toggle__option--active': currentTheme === option.value,
            }"
            role="radio"
            :aria-checked="currentTheme === option.value"
            :aria-label="`Use ${option.label} theme`"
            @click="selectTheme(option.value)"
        >
            {{ option.label }}
        </button>
    </div>
</template>

<style scoped>
.theme-toggle {
    position: relative;
    display: inline-grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: center;
    min-height: 2.9rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 90%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-surface) 78%, transparent);
    padding: 3px;
    backdrop-filter: blur(14px);
    box-shadow: var(--sw-shadow-sm);
}

.theme-toggle__thumb {
    position: absolute;
    top: 3px;
    bottom: 3px;
    left: 3px;
    width: calc(50% - 3px);
    border-radius: var(--sw-radius-full);
    background: var(--sw-bg-elevated);
    box-shadow: var(--sw-shadow-sm);
    transition:
        transform var(--sw-motion-smooth),
        background-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.theme-toggle__option {
    position: relative;
    z-index: 1;
    min-width: 5.5rem;
    min-height: calc(2.9rem - 6px);
    border: 0;
    border-radius: var(--sw-radius-full);
    background: transparent;
    font-family: var(--sw-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-tab-inactive);
    transition:
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.theme-toggle__option--active {
    color: var(--sw-tab-active);
}

.theme-toggle__option:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .theme-toggle__option:hover {
        transform: translateY(-1px);
        color: var(--sw-text-primary);
    }
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
