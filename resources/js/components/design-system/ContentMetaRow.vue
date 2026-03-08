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
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.content-meta-row__item {
    display: inline-flex;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 7px;
    min-height: 2.125rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-full);
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-elevated) 20%, transparent),
            transparent
        ),
        color-mix(in srgb, var(--sw-bg-grid) 68%, transparent);
    padding: 0.52rem 0.95rem;
    box-shadow: inset 0 1px 0 color-mix(in srgb, white 18%, transparent);
}

.content-meta-row__label {
    font-family: var(--sw-font-heading);
    font-size: 10px;
    font-weight: 700;
    line-height: 1.45;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
    transform: translateY(1px);
}

.content-meta-row__value {
    font-family: var(--sw-font-code);
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: 0.02em;
    font-variant-numeric: tabular-nums;
    color: var(--sw-text-primary);
    transform: translateY(1px);
}

@media (max-width: 640px) {
    .content-meta-row {
        gap: 8px;
    }

    .content-meta-row__item {
        min-height: 2rem;
        padding-inline: 0.75rem;
    }
}
</style>
