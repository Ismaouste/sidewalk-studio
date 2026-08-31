<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTheme } from '@/composables/useTheme';
import { copy as copyTree } from '@/copy';
import type { SiteProps } from '@/types';

const props = withDefaults(
    defineProps<{
        compact?: boolean;
    }>(),
    {
        compact: false,
    },
);

const { currentTheme, setTheme } = useTheme();
const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].layout.landmarks);

/**
 * The two theme names are the design system's own nouns, not copy: `morning`
 * and `sunset` are what `docs/style/` calls them and what `data-theme` carries.
 * They stay untranslated on purpose.
 */
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
        :class="{ 'theme-toggle--compact': props.compact }"
        role="radiogroup"
        :aria-label="copy.colorTheme"
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
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-surface) 78%, transparent);
    padding: 3px;
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

.theme-toggle__thumb {
    position: absolute;
    top: 3px;
    bottom: 3px;
    left: 3px;
    width: calc(50% - 3px);
    border-radius: calc(var(--sw-radius-lg) - 2px);
    background: var(--sw-bg-elevated);
    transition:
        transform var(--sw-motion-smooth),
        background-color var(--sw-motion-fast);
}

.theme-toggle__option {
    position: relative;
    z-index: 1;
    min-width: 5.5rem;
    min-height: calc(2.9rem - 6px);
    border: 0;
    border-radius: 3px;
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

.theme-toggle--compact {
    min-height: 2.45rem;
}

.theme-toggle--compact .theme-toggle__option {
    min-width: 4.4rem;
    min-height: calc(2.45rem - 6px);
    font-size: 9px;
}
</style>
