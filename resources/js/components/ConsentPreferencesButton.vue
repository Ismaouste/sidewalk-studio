<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

function openPreferences() {
    window.SidewalkConsent?.showPreferences();
}
</script>

<template>
    <button
        type="button"
        class="consent-preferences-button"
        @click="openPreferences"
    >
        {{ page.props.site.shell.privacyControlsLabel }}
    </button>
</template>

<style scoped>
.consent-preferences-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.45rem;
    gap: var(--sw-space-3xs);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-full);
    background: transparent;
    padding-inline: 0.85rem;
    font-family: var(--sw-font-body);
    font-size: 13px;
    font-weight: 500;
    color: var(--sw-text-primary);
    transition:
        background-color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.consent-preferences-button:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .consent-preferences-button:hover {
        transform: translateY(-1px);
        border-color: var(--sw-accent-green);
        background: color-mix(in srgb, var(--sw-bg-elevated) 64%, transparent);
        box-shadow: var(--sw-shadow-sm);
    }
}
</style>
