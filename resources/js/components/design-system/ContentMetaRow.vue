<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { copy as copyTree } from '@/copy';
import type { SiteProps } from '@/types';

defineProps<{
    items: Array<{
        label?: string;
        value: string;
        tone?: 'default' | 'sun' | 'green' | 'coral' | 'violet';
    }>;
}>();

/**
 * The only global this primitive reads, and it reads it for one reason: the
 * list is an `aria-label`led region, so the label is copy, and copy does not
 * belong inline in a component. Passing it from all five call sites would put
 * the same import in five pages to say the same thing.
 */
const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].layout.landmarks);
</script>

<template>
    <ul class="content-meta-row" :aria-label="copy.contentMeta">
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
    gap: 0;
    margin: 0;
    padding: 0;
    list-style: none;
}

.content-meta-row__item {
    display: inline-flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 6px;
    min-width: 0;
    max-width: min(100%, 26rem);
}

.content-meta-row__item::after {
    content: '·';
    margin-inline: 10px;
    color: var(--sw-text-muted);
}

.content-meta-row__item:last-child::after {
    content: none;
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
    min-width: 0;
    overflow-wrap: anywhere;
    word-break: break-word;
    text-wrap: pretty;
}

.content-meta-row__item--sun .content-meta-row__value {
    color: color-mix(
        in srgb,
        var(--sw-accent-sun) 78%,
        var(--sw-text-secondary)
    );
}

@media (max-width: 640px) {
    .content-meta-row {
        row-gap: 4px;
    }

    .content-meta-row__item {
        max-width: 100%;
    }
}
</style>
