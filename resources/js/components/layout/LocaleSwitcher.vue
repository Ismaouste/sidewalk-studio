<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const switcher = computed(() => page.props.site.languageSwitcher);
</script>

<template>
    <nav
        v-if="switcher.visible"
        class="locale-switcher"
        :aria-label="page.props.site.shell.localeSwitcherLabel"
    >
        <Link
            v-for="option in switcher.options"
            :key="option.code"
            :href="option.href ?? page.url"
            class="locale-switcher__option"
            :class="{
                'locale-switcher__option--current':
                    option.code === switcher.current,
                'locale-switcher__option--disabled': !option.available,
            }"
            :aria-current="
                option.code === switcher.current ? 'true' : undefined
            "
            :tabindex="option.available ? undefined : -1"
            :aria-disabled="option.available ? undefined : 'true'"
            preserve-scroll
        >
            {{ option.label }}
        </Link>
    </nav>
</template>

<style scoped>
.locale-switcher {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    min-height: 2.75rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-surface) 86%, transparent);
    padding: 4px;
    box-shadow: var(--sw-shadow-sm);
}

.locale-switcher__option {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.75rem;
    min-height: 2.25rem;
    border-radius: calc(var(--sw-radius-full) - 4px);
    padding-inline: 0.7rem;
    font-family: var(--sw-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-tab-inactive);
    text-decoration: none;
    transition:
        background-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.locale-switcher__option--current {
    background: var(--sw-bg-elevated);
    color: var(--sw-text-primary);
    box-shadow: inset 0 0 0 1px
        color-mix(in srgb, var(--sw-accent-dominant) 16%, transparent);
}

.locale-switcher__option--disabled {
    opacity: 0.42;
    pointer-events: none;
}

.locale-switcher__option:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .locale-switcher__option:hover {
        color: var(--sw-text-primary);
        background: color-mix(in srgb, var(--sw-bg-elevated) 84%, transparent);
    }
}
</style>
