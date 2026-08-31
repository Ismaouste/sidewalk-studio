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
    color: color-mix(
        in srgb,
        var(--sw-text-muted) 86%,
        var(--sw-text-secondary)
    );
    text-transform: none;
    letter-spacing: 0.01em;
    font-size: 0.78rem;
    transition: color var(--sw-motion-fast);
}

.breadcrumb-trail__current {
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 92%,
        var(--sw-text-primary)
    );
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
        padding: 0.16rem var(--sw-space-xs) 0;
        border-bottom: 1px solid transparent;
    }

    .breadcrumb-trail__list {
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 4px;
        padding: 0.08rem 0 0;
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
        padding-inline: 0.35rem;
        white-space: nowrap;
        font-size: 0.78rem;
        line-height: 1.2;
    }

    .breadcrumb-trail__current {
        color: var(--sw-text-primary);
    }
}

/* Knowing whether the bar has reached the header used to mean a scroll
   listener, a rAF, a getBoundingClientRect and a getComputedStyle on every
   frame — four main-thread reads to answer one yes-or-no question. The
   sentinel that answers it now lives in SiteLayout, because a sticky element
   can only stick within its containing block: wrapping this nav to hold its
   own sentinel would have left it nothing to stick to.
   Without support the bar stays sticky and simply never gains the backdrop,
   which is why the animation is gated rather than merely declared. */
@supports (animation-timeline: view()) {
    @media (max-width: 640px) {
        .breadcrumb-trail {
            animation-name: breadcrumb-stuck;
            animation-fill-mode: both;
            /* A switch, not a fade: the bar is either against the header or
               it is not. */
            animation-timing-function: step-end;
            animation-timeline: --sw-breadcrumb-sentinel;
            animation-range: exit;
        }

        /* reset.css blanks `animation` under reduced motion, and it is right
           to: that rule is aimed at time-based movement. This animation has no
           duration and moves nothing — it reports a position. Dropping it
           would leave the breadcrumb transparent over the content scrolling
           underneath it, so it opts back in. */
        html[data-motion='reduced'] .breadcrumb-trail {
            animation-name: breadcrumb-stuck !important;
            animation-fill-mode: both !important;
            animation-timing-function: step-end !important;
            animation-timeline: --sw-breadcrumb-sentinel !important;
            animation-range: exit !important;
        }
    }
}

@keyframes breadcrumb-stuck {
    to {
        -webkit-backdrop-filter: blur(18px) saturate(112%);
        backdrop-filter: blur(18px) saturate(112%);
    }
}
</style>
