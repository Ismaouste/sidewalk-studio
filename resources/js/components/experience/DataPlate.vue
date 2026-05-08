<script setup lang="ts">
defineProps<{
    trajectory: Array<{ title: string; summary: string }>;
    strengths: string[];
    focusAreas: Array<{ title: string; summary: string }>;
    trajectoryLabel: string;
    strengthsLabel: string;
    focusAreasLabel: string;
}>();
</script>

<template>
    <section class="data-plate">
        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">{{ trajectoryLabel }}</p>
            <ul class="data-plate__list">
                <li
                    v-for="row in trajectory"
                    :key="row.title"
                    class="data-plate__item"
                >
                    <p class="type-h3 data-plate__item-title">
                        {{ row.title }}
                    </p>
                    <p class="type-body data-plate__item-summary">
                        {{ row.summary }}
                    </p>
                </li>
            </ul>
        </div>

        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">{{ strengthsLabel }}</p>
            <ul class="data-plate__list data-plate__list--terse">
                <li
                    v-for="line in strengths"
                    :key="line"
                    class="data-plate__item-terse"
                >
                    {{ line }}
                </li>
            </ul>
        </div>

        <div class="data-plate__column">
            <p class="type-eyebrow data-plate__label">{{ focusAreasLabel }}</p>
            <ul class="data-plate__list">
                <li
                    v-for="row in focusAreas"
                    :key="row.title"
                    class="data-plate__item"
                >
                    <p class="type-h3 data-plate__item-title">
                        {{ row.title }}
                    </p>
                    <p class="type-body data-plate__item-summary">
                        {{ row.summary }}
                    </p>
                </li>
            </ul>
        </div>
    </section>
</template>

<style scoped>
.data-plate {
    display: grid;
    gap: var(--sw-space-md);
    padding: var(--sw-space-md);
    border: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
    min-width: 0;
}

@media (min-width: 920px) {
    .data-plate {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.data-plate__column {
    display: grid;
    gap: var(--sw-space-xs);
    min-width: 0;
}

.data-plate__label {
    margin: 0;
    color: var(--sw-accent-violet);
}

.data-plate__list {
    display: grid;
    gap: var(--sw-space-xs);
    margin: 0;
    padding: 0;
    list-style: none;
}

.data-plate__list--terse {
    gap: var(--sw-space-3xs);
}

.data-plate__item {
    display: grid;
    gap: 4px;
    padding-top: var(--sw-space-3xs);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 56%, transparent);
}

.data-plate__item:first-child {
    border-top: 0;
    padding-top: 0;
}

.data-plate__item-title {
    margin: 0;
    color: var(--sw-text-primary);
}

.data-plate__item-summary {
    margin: 0;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.data-plate__item-terse {
    margin: 0;
    font-size: 0.96rem;
    line-height: 1.55;
    color: var(--sw-text-secondary);
}

.data-plate__item-terse::before {
    content: '— ';
    color: var(--sw-accent-dominant);
    margin-right: 0.3em;
}
</style>
