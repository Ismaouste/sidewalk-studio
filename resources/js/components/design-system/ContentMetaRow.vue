<script setup lang="ts">
defineProps<{
    items: Array<{
        label?: string;
        value: string;
        tone?: 'default' | 'sun' | 'green' | 'coral' | 'violet';
    }>;
}>();
</script>

<template>
    <ul class="content-meta-row" aria-label="Content metadata">
        <li
            v-for="item in items"
            :key="`${item.label ?? 'meta'}-${item.value}`"
            class="content-meta-row__item"
            :class="{
                [`content-meta-row__item--${item.tone}`]:
                    item.tone && item.tone !== 'default',
            }"
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
    gap: 16px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.content-meta-row__item {
    display: inline-flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 6px;
    max-width: min(100%, 26rem);
}

.content-meta-row__label {
    font-family: var(--sw-font-body);
    font-size: 10px;
    font-weight: 500;
    line-height: 1.4;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
}

.content-meta-row__value {
    font-family: var(--sw-font-body);
    font-size: 13px;
    line-height: 1.35;
    letter-spacing: 0.02em;
    font-variant-numeric: tabular-nums;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.content-meta-row__item--sun .content-meta-row__value {
    color: color-mix(in srgb, var(--sw-accent-sun) 78%, var(--sw-text-secondary));
}

@media (max-width: 640px) {
    .content-meta-row {
        gap: 12px;
    }
}
</style>
