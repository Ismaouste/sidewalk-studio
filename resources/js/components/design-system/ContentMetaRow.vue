<script setup lang="ts">
defineProps<{
    items: Array<{
        label?: string;
        value: string;
    }>;
}>();
</script>

<template>
    <ul class="content-meta-row" aria-label="Content metadata">
        <li
            v-for="item in items"
            :key="`${item.label ?? 'meta'}-${item.value}`"
            class="content-meta-row__item"
        >
            <span v-if="item.label" class="content-meta-row__label">
                {{ item.label }}
            </span>
            <span class="content-meta-row__value">{{ item.value }}</span>
        </li>
    </ul>
</template>

<style scoped>
.content-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.content-meta-row__item {
    display: inline-flex;
    align-items: center;
    gap: var(--sw-space-4xs);
    min-height: 2rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-grid) 68%, transparent);
    padding-inline: 0.85rem;
}

.content-meta-row__label {
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.content-meta-row__value {
    font-family: var(--sw-font-body);
    font-size: 13px;
    line-height: 1;
    color: var(--sw-text-primary);
}
</style>
