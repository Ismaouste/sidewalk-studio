<script setup lang="ts">
type MetricItem = {
    label: string;
    value: string;
    note?: string;
};

defineProps<{
    items: MetricItem[];
}>();
</script>

<template>
    <section class="metric-strip sw-surface">
        <article
            v-for="item in items"
            :key="`${item.label}-${item.value}`"
            class="metric-strip__item"
        >
            <p class="metric-strip__value">{{ item.value }}</p>
            <p class="type-nav metric-strip__label">{{ item.label }}</p>
            <p v-if="item.note" class="type-meta metric-strip__note">
                {{ item.note }}
            </p>
        </article>
    </section>
</template>

<style scoped>
.metric-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
    gap: 0;
    overflow: hidden;
}

.metric-strip__item {
    display: grid;
    gap: var(--sw-space-3xs);
    padding: var(--sw-space-sm);
    border-right: 1px solid var(--sw-border);
}

.metric-strip__item:last-child {
    border-right: 0;
}

.metric-strip__value {
    margin: 0;
    font-family: var(--sw-font-display);
    font-size: clamp(28px, 4vw, 42px);
    font-style: italic;
    font-weight: 300;
    line-height: 1;
    color: var(--sw-text-primary);
}

.metric-strip__label {
    color: var(--sw-text-primary);
}

.metric-strip__note {
    margin: 0;
}

@media (max-width: 768px) {
    .metric-strip__item {
        border-right: 0;
        border-bottom: 1px solid var(--sw-border);
    }

    .metric-strip__item:last-child {
        border-bottom: 0;
    }
}
</style>
