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
    align-items: center;
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
    padding: 0.38rem 0.85rem;
    box-shadow: inset 0 1px 0 color-mix(in srgb, white 18%, transparent);
}

.content-meta-row__label {
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
}

.content-meta-row__value {
    font-family: var(--sw-font-body);
    font-size: 13px;
    line-height: 1.15;
    color: var(--sw-text-primary);
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
