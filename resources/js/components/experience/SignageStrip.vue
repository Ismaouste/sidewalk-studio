<script setup lang="ts">
defineProps<{
    items: Array<{
        id: string;
        eyebrow: string;
        label: string;
        dateRange?: string;
    }>;
    ariaLabel?: string;
}>();
</script>

<template>
    <nav class="signage-strip" :aria-label="ariaLabel ?? 'Section navigation'">
        <a
            v-for="item in items"
            :key="item.id"
            :href="`#${item.id}`"
            class="signage-strip__chip"
        >
            <span v-if="item.dateRange" class="signage-strip__date">{{
                item.dateRange
            }}</span>
            <span class="signage-strip__label">{{ item.label }}</span>
            <span class="signage-strip__eyebrow">{{ item.eyebrow }}</span>
        </a>
    </nav>
</template>

<style scoped>
.signage-strip {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    padding: var(--sw-space-3xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-surface);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
    min-width: 0;
}

.signage-strip__chip {
    display: inline-grid;
    gap: 2px;
    padding: var(--sw-space-3xs) var(--sw-space-xs);
    border-radius: var(--sw-radius-md);
    color: var(--sw-text-primary);
    text-decoration: none;
    transition:
        background-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.signage-strip__chip:hover {
    background: color-mix(in srgb, var(--sw-accent-coral) 14%, transparent);
    transform: translateY(-1px);
}

.signage-strip__chip:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

.signage-strip__date {
    font-family: var(--sw-font-code, monospace);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: var(--sw-accent-coral);
    text-transform: none;
    line-height: 1.2;
}

.signage-strip__label {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.2;
}

.signage-strip__eyebrow {
    font-family: var(--sw-font-body);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.02em;
    text-transform: none;
    color: var(--sw-text-muted);
    line-height: 1.3;
}

@media (prefers-reduced-motion: reduce) {
    .signage-strip__chip {
        transition: none;
    }
    .signage-strip__chip:hover {
        transform: none;
    }
}

@media (min-width: 1040px) {
    .signage-strip {
        position: sticky;
        top: calc(var(--sw-header-offset) + var(--sw-space-xs));
        z-index: var(--sw-z-content);
    }
}

@media (max-width: 640px) {
    .signage-strip {
        overflow-x: auto;
        flex-wrap: nowrap;
        scrollbar-width: thin;
    }
}
</style>
