<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { copy as copyTree } from '@/copy';
import type { BreadcrumbItem, SiteProps } from '@/types';

defineProps<{
    items: BreadcrumbItem[];
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(() => copyTree[page.props.site.locale].layout.landmarks);
</script>

<template>
    <nav class="breadcrumb-trail" :aria-label="copy.breadcrumb">
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

/* A trail that wraps onto a second line stops reading as a trail and starts
   reading as a paragraph, so it stays on one line and the current page gives
   up its width instead. */
.breadcrumb-trail__list {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: var(--sw-space-4xs);
    margin: 0;
    padding: 0;
    list-style: none;
    min-width: 0;
}

.breadcrumb-trail__item {
    display: inline-flex;
    align-items: center;
    gap: var(--sw-space-4xs);
    /* The ancestors are the trail; they keep their full width. */
    flex: none;
    min-width: 0;
}

/* Only the last crumb yields. It is the one the reader can already see in the
   page heading below, so it is the one that can afford to be cut. */
.breadcrumb-trail__item:last-child {
    flex: 0 1 auto;
    overflow: hidden;
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
    /* `text-overflow` only applies to a block container, so this one cannot be
       the flex box its siblings are. It keeps their line-height rather than
       inventing one — a taller line box puts the glyphs on a different
       baseline, which is what made this crumb sit a fraction below the others
       — and gives up its min-height so the parent item centres it instead. */
    display: block;
    min-height: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    min-width: 0;
}

@media (hover: hover) {
    .breadcrumb-trail__link:hover {
        color: var(--sw-text-primary);
    }
}

@media (max-width: 640px) {
    /* The bar bleeds the shell's gutter on both sides, so the glass it picks up
       when it sticks meets both screen edges rather than stopping just inside
       them. It reads the gutter from the container instead of restating it:
       that number narrows twice more below this breakpoint, and a literal here
       was wrong on a phone in one direction and short in the other. */
    .breadcrumb-trail {
        position: sticky;
        top: calc(var(--sw-public-header-height, 104px) - 1px);
        z-index: calc(var(--sw-z-header) - 2);
        margin-inline: calc(-1 * var(--sw-container-inset, var(--sw-space-xs)));
        padding-block: 0.16rem 0;
        padding-inline: var(--sw-container-inset, var(--sw-space-xs));
        border-bottom: 1px solid transparent;
    }

    .breadcrumb-trail__list {
        overflow-x: auto;
        padding: 0.08rem 0 0;
        scrollbar-width: none;
    }

    /* On a phone the trail scrolls rather than truncates: there is no room to
       shorten into, and swiping keeps the whole path reachable. */
    .breadcrumb-trail__item:last-child {
        flex: none;
        overflow: visible;
    }

    .breadcrumb-trail__current {
        overflow: visible;
        text-overflow: clip;
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

/* The glass the bar picks up once it is against the header. Reading the token
   rather than a literal is what keeps it in step with every other blurred
   surface: morning and sunset define different blur radii and saturations,
   and sunset's has to stay above 1. */
@keyframes breadcrumb-stuck {
    to {
        -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
        backdrop-filter: var(--sw-surface-backdrop-filter);
    }
}
</style>
