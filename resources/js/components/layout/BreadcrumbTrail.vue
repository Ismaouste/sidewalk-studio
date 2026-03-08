<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    items: BreadcrumbItem[];
}>();
</script>

<template>
    <nav class="breadcrumb-trail" aria-label="Breadcrumb">
        <ol class="breadcrumb-trail__list">
            <li
                v-for="(item, index) in items"
                :key="`${item.href}-${index}`"
                class="breadcrumb-trail__item"
            >
                <Link
                    v-if="index < items.length - 1"
                    :href="item.href"
                    class="type-meta breadcrumb-trail__link"
                >
                    {{ item.label }}
                </Link>
                <span
                    v-else
                    aria-current="page"
                    class="type-meta breadcrumb-trail__current"
                >
                    {{ item.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>

<style scoped>
.breadcrumb-trail {
    display: block;
    background: transparent;
}

.breadcrumb-trail__list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.breadcrumb-trail__item {
    display: inline-flex;
    align-items: center;
    gap: var(--sw-space-4xs);
}

.breadcrumb-trail__item:not(:last-child)::after {
    content: '/';
    color: color-mix(in srgb, var(--sw-text-muted) 84%, transparent);
}

.breadcrumb-trail__link,
.breadcrumb-trail__current {
    display: inline-flex;
    align-items: center;
    min-height: 1.8rem;
    padding-inline: 0.15rem;
    color: color-mix(in srgb, var(--sw-text-muted) 86%, var(--sw-text-secondary));
    transition: color var(--sw-motion-fast);
}

.breadcrumb-trail__current {
    color: color-mix(in srgb, var(--sw-text-secondary) 92%, var(--sw-text-primary));
}

@media (hover: hover) {
    .breadcrumb-trail__link:hover {
        color: var(--sw-text-primary);
    }
}

@media (max-width: 640px) {
    .breadcrumb-trail {
        position: sticky;
        top: calc(var(--sw-public-header-height, 104px) - 1px);
        z-index: calc(var(--sw-z-header) - 2);
        margin-inline: calc(-1 * var(--sw-space-xs));
        padding: 0.25rem var(--sw-space-xs) 0.1rem;
        background:
            linear-gradient(
                180deg,
                color-mix(in srgb, var(--sw-bg-base) 84%, transparent),
                color-mix(in srgb, var(--sw-bg-base) 58%, transparent)
            );
        border-bottom: 1px solid color-mix(
            in srgb,
            var(--sw-border) 56%,
            transparent
        );
        -webkit-backdrop-filter: blur(12px) saturate(128%);
        backdrop-filter: blur(12px) saturate(128%);
    }

    .breadcrumb-trail__list {
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 4px;
        padding: 0.15rem 0;
        scrollbar-width: none;
    }

    .breadcrumb-trail__list::-webkit-scrollbar {
        display: none;
    }

    .breadcrumb-trail__link,
    .breadcrumb-trail__current {
        display: inline-flex;
        align-items: center;
        min-height: 2.2rem;
        padding-inline: 0.35rem;
        white-space: nowrap;
        font-size: 0.78rem;
        line-height: 1.2;
    }

    .breadcrumb-trail__current {
        color: var(--sw-text-primary);
    }
}
</style>
