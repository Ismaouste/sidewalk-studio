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
}

.breadcrumb-trail__list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-4xs);
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
    color: var(--sw-text-muted);
}

.breadcrumb-trail__link,
.breadcrumb-trail__current {
    color: var(--sw-text-muted);
}

.breadcrumb-trail__current {
    color: var(--sw-text-secondary);
}

@media (hover: hover) {
    .breadcrumb-trail__link:hover {
        color: var(--sw-text-primary);
    }
}

@media (max-width: 640px) {
    .breadcrumb-trail {
        position: sticky;
        top: calc(env(safe-area-inset-top) + 4.4rem);
        z-index: calc(var(--sw-z-header) - 1);
        margin-inline: calc(-1 * var(--sw-space-xs));
        padding: 0 var(--sw-space-xs);
    }

    .breadcrumb-trail__list {
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 0;
        padding: 0.55rem 0.7rem;
        border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
        border-radius: var(--sw-radius-full);
        background: color-mix(in srgb, var(--sw-bg-surface) 88%, transparent);
        box-shadow: var(--sw-shadow-sm);
        -webkit-backdrop-filter: blur(16px) saturate(140%);
        backdrop-filter: blur(16px) saturate(140%);
        scrollbar-width: none;
    }

    .breadcrumb-trail__list::-webkit-scrollbar {
        display: none;
    }

    .breadcrumb-trail__link,
    .breadcrumb-trail__current {
        display: inline-flex;
        align-items: center;
        min-height: 2rem;
        padding-inline: 0.2rem;
        white-space: nowrap;
        font-size: 0.72rem;
    }

    .breadcrumb-trail__current {
        color: var(--sw-text-primary);
    }
}
</style>
