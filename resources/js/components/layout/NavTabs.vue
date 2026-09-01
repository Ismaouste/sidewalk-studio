<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { copy as copyTree } from '@/copy';
import { isNavPath } from '@/copy/navPath';
import type { NavItem, SiteProps } from '@/types';

const props = defineProps<{
    items: NavItem[];
}>();

/** Kept in step with the `min-width: 960px` block in this file's styles. */
const DESKTOP_QUERY = '(min-width: 960px)';

const page = usePage<{ site: SiteProps }>();
const menuId = 'primary-navigation';
const menuRef = ref<HTMLElement | null>(null);
const isOpen = ref(false);

const activeItem = computed(
    () => props.items.find((item) => item.active) ?? null,
);

function linkAction(item: NavItem): string {
    return isNavPath(item.path)
        ? copyTree[page.props.site.locale].layout.navigation.action[item.path]
        : page.props.site.shell.navOpenLabel;
}

function closeMenu(): void {
    if (menuRef.value?.matches(':popover-open')) {
        menuRef.value.hidePopover();
    }
}

// The popover attribute already covers opening, light dismiss, Escape and the
// top layer. Three cases are left over, and they are the only reason this
// component still runs any script:
//
// 1. Chromium does not currently expose the invoker's expanded state to
//    assistive technology, so the disclosure has to say it out loud. The UA
//    stays the source of truth — `toggle` reports what it just did, and
//    nothing here ever decides the state itself. The trigger's open styling
//    reads the sheet directly with :has(), so only ARIA depends on this.
function handleToggle(event: ToggleEvent): void {
    isOpen.value = event.newState === 'open';
}

// 2. The header lives in the persistent layout, so a visit started from inside
//    the sheet leaves it open over the new page. Nothing closes a popover when
//    a link inside it navigates.
watch(
    () => page.url,
    () => {
        closeMenu();
    },
);

// 3. An open popover stays in the top layer whatever `position` says, so the
//    desktop media query cannot fold the sheet back into the header row on its
//    own. Crossing the breakpoint has to close it.
let desktopQuery: MediaQueryList | null = null;

function handleDesktopChange(event: MediaQueryListEvent): void {
    if (event.matches) {
        closeMenu();
    }
}

onMounted(() => {
    desktopQuery = window.matchMedia(DESKTOP_QUERY);
    desktopQuery.addEventListener('change', handleDesktopChange);
});

onBeforeUnmount(() => {
    desktopQuery?.removeEventListener('change', handleDesktopChange);
});
</script>

<template>
    <nav class="nav-tabs" :aria-label="page.props.site.shell.navAriaLabel">
        <button
            type="button"
            class="nav-tabs__trigger"
            :popovertarget="menuId"
            :aria-expanded="isOpen"
        >
            <span class="nav-tabs__trigger-copy">
                <span class="nav-tabs__trigger-label">
                    {{ page.props.site.shell.navMenuLabel }}
                </span>
                <span class="nav-tabs__trigger-current">
                    {{
                        activeItem?.label ??
                        page.props.site.shell.navFallbackLabel
                    }}
                </span>
            </span>
            <span class="nav-tabs__trigger-icon" aria-hidden="true">
                <span />
                <span />
            </span>
        </button>

        <div
            :id="menuId"
            ref="menuRef"
            popover
            class="nav-tabs__panel"
            @toggle="handleToggle"
        >
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                prefetch="hover"
                cache-for="30s"
                class="nav-tabs__link"
                :class="{ 'nav-tabs__link--active': item.active }"
            >
                <span class="nav-tabs__link-label">{{ item.label }}</span>
                <span
                    class="nav-tabs__link-meta"
                    :class="{
                        'nav-tabs__link-meta--contact':
                            item.path === '/contact',
                    }"
                >
                    <span>{{ linkAction(item) }}</span>
                </span>
            </Link>
        </div>
    </nav>
</template>

<style scoped>
.nav-tabs {
    --nav-tabs-sheet-width: 380px;

    position: relative;
    display: grid;
    width: 100%;
    max-width: var(--nav-tabs-sheet-width);
    justify-self: start;
    justify-items: stretch;
}

.nav-tabs__trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 3.25rem;
    gap: var(--sw-space-xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 86%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-base);
    padding-inline: 1rem;
    color: var(--sw-text-primary);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        color var(--sw-motion-fast);
}

.nav-tabs__trigger-copy {
    display: grid;
    gap: 2px;
    padding-left: 4px;
    text-align: left;
}

.nav-tabs__trigger-label {
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
}

.nav-tabs__trigger-current {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.15;
}

.nav-tabs__trigger-icon {
    position: relative;
    width: 18px;
    height: 14px;
    flex: none;
}

.nav-tabs__trigger-icon span {
    position: absolute;
    left: 0;
    width: 100%;
    height: 2px;
    border-radius: var(--sw-radius-pill);
    background: currentColor;
    transition: transform var(--sw-motion-fast);
}

.nav-tabs__trigger-icon span:first-child {
    top: 3px;
}

.nav-tabs__trigger-icon span:last-child {
    bottom: 3px;
}

/* The sheet is a sibling in the DOM even while it paints in the top layer,
   so the trigger can read its state without holding a copy of it. */
.nav-tabs:has(.nav-tabs__panel:popover-open) .nav-tabs__trigger {
    border-color: color-mix(in srgb, var(--sw-border) 92%, transparent);
    background: var(--sw-bg-grid);
}

.nav-tabs:has(.nav-tabs__panel:popover-open)
    .nav-tabs__trigger-icon
    span:first-child {
    transform: translateY(3px) rotate(45deg);
}

.nav-tabs:has(.nav-tabs__panel:popover-open)
    .nav-tabs__trigger-icon
    span:last-child {
    transform: translateY(-3px) rotate(-45deg);
}

/* The UA sheet hands every [popover] a centred fixed box with a border, a
   system background and `display: none` when closed. All of it is UA-origin,
   so these author declarations win — including the desktop block below, which
   is why the row needs no JavaScript to exist. Being in the top layer also
   means the sheet needs no z-index: it cannot be covered.

   `display` is the one declaration that must not be here, and it was. Beating
   the UA on it is the desktop block's whole trick, but doing it unconditionally
   meant the closed sheet stayed laid out below the breakpoint too: a 373x190
   box under the header, invisible at `opacity: 0`, swallowing every tap inside
   it on every page. It is declared on `:popover-open` and in the desktop block,
   and nowhere else. */
.nav-tabs__panel {
    position: fixed;
    inset: calc(var(--sw-public-header-height, 104px) + var(--sw-space-4xs))
        auto auto calc(var(--sw-layout-gutter-md) / 2);
    width: min(
        var(--nav-tabs-sheet-width),
        calc(100% - var(--sw-layout-gutter-md))
    );
    max-width: none;
    height: auto;
    max-height: none;
    margin: 0;
    gap: 10px;
    padding: var(--sw-space-2xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 92%, transparent);
    border-radius: calc(var(--sw-radius-lg) + 2px);
    background: var(--sw-bg-base);
    color: var(--sw-text-primary);
    overflow: visible;
    opacity: 0;
    translate: 0 calc(-1 * var(--sw-space-4xs));
    transition:
        opacity var(--sw-motion-fast),
        translate var(--sw-motion-fast),
        display var(--sw-motion-fast) allow-discrete,
        overlay var(--sw-motion-fast) allow-discrete;
}

.nav-tabs__panel:popover-open {
    display: grid;
    opacity: 1;
    translate: none;
}

/* Without a starting state the sheet would pop in at full opacity: it has no
   previous computed style to transition from on the frame it stops being
   `display: none`. */
@starting-style {
    .nav-tabs__panel:popover-open {
        opacity: 0;
        translate: 0 calc(-1 * var(--sw-space-4xs));
    }
}

.nav-tabs__link {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 3.25rem;
    gap: var(--sw-space-xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-bg-grid);
    padding: 0.9rem 1rem;
    color: var(--sw-text-primary);
    transition:
        background-color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        color var(--sw-motion-fast);
}

.nav-tabs__link::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    background:
        radial-gradient(
            circle at 18% 22%,
            color-mix(
                in srgb,
                var(--sw-accent-dominant) 14%,
                var(--sw-bg-grid)
            ),
            transparent 44%
        ),
        linear-gradient(
            140deg,
            color-mix(in srgb, white 8%, transparent),
            transparent 62%
        );
    opacity: 0;
    transform: scale(0.96);
    transition:
        opacity var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.nav-tabs__link::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    opacity: 0;
    background: color-mix(in srgb, var(--sw-bg-base) 82%, var(--sw-bg-grid));
    transition: opacity var(--sw-motion-fast);
}

.nav-tabs__link > * {
    position: relative;
    z-index: 1;
}

.nav-tabs__link-label {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
}

.nav-tabs__link-meta {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
}

.nav-tabs__link-meta::after {
    content: '\2192';
    display: inline-block;
    font-size: 12px;
    line-height: 1;
    transform: translateY(-1px);
}

.nav-tabs__link-meta--contact::after {
    transform-origin: center;
}

.nav-tabs__link--active {
    border-color: color-mix(
        in srgb,
        var(--sw-accent-dominant) 22%,
        var(--sw-border)
    );
    background: color-mix(
        in srgb,
        var(--sw-tab-surface, var(--sw-bg-elevated)) 92%,
        var(--sw-bg-surface)
    );
    color: var(--sw-tab-active);
}

.nav-tabs__link--active .nav-tabs__link-label {
    font-weight: 700;
}

@media (hover: hover) {
    .nav-tabs__trigger:hover {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
        background: var(--sw-bg-grid);
    }

    .nav-tabs__link:hover {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
        background: color-mix(
            in srgb,
            var(--sw-bg-base) 76%,
            var(--sw-bg-grid)
        );
        color: var(--sw-text-primary);
    }

    .nav-tabs__link:hover::before,
    .nav-tabs__link:focus-visible::before {
        opacity: 1;
    }

    .nav-tabs__link:hover .nav-tabs__link-meta--contact::after,
    .nav-tabs__link:focus-visible .nav-tabs__link-meta--contact::after {
        animation: nav-tabs-phone-ring 0.8s ease;
    }
}

@media (max-width: 959px) {
    .nav-tabs__link::before,
    .nav-tabs__link::after {
        display: none;
    }

    /* The sheet lists where you can go, not where you are — the trigger
       already names the current section. Filtering it out used to need a
       media-query listener and a second derived list. */
    .nav-tabs__link--active {
        display: none;
    }

    .nav-tabs__link:is(:hover, :focus-visible, :active) {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 30%,
            var(--sw-border)
        );
        background: var(--sw-bg-grid);
    }

    .nav-tabs__link:is(:hover, :focus-visible, :active)
        .nav-tabs__link-meta--contact::after {
        animation: nav-tabs-phone-ring 0.8s ease;
    }
}

@media (min-width: 960px) {
    .nav-tabs {
        display: flex;
        width: auto;
        max-width: none;
        flex-wrap: wrap;
        justify-content: flex-end;
        justify-items: stretch;
        gap: var(--sw-space-2xs);
    }

    .nav-tabs__trigger {
        display: none;
    }

    /* Above the breakpoint the panel stops being a sheet and becomes the tab
       row. Every declaration here overrides either a UA popover style or its
       mobile counterpart, so the element never has to lose its `popover`
       attribute. */
    .nav-tabs__panel {
        display: flex;
        position: static;
        inset: auto;
        width: auto;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: var(--sw-space-2xs);
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        overflow: visible;
        opacity: 1;
        translate: none;
        transition: none;
    }

    .nav-tabs__link {
        position: relative;
        isolation: isolate;
        min-height: 0;
        width: auto;
        border: 0;
        border-radius: var(--sw-radius-md);
        background: transparent;
        padding: calc(var(--sw-space-4xs) + 2px) var(--sw-space-xs);
        font-family: var(--sw-font-heading);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sw-tab-inactive);
        overflow: hidden;
        transition: color var(--sw-motion-fast);
    }

    .nav-tabs__link::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 0;
        border-radius: inherit;
        background: color-mix(
            in srgb,
            var(--sw-tab-surface, var(--sw-bg-elevated)) 78%,
            transparent
        );
        opacity: 0;
        transform: scale(0.92);
        transition:
            opacity var(--sw-motion-fast),
            transform var(--sw-motion-fast),
            background-color var(--sw-motion-fast);
    }

    .nav-tabs__link-label {
        font-family: inherit;
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
        position: relative;
        z-index: 1;
    }

    .nav-tabs__link-meta {
        display: none;
    }

    .nav-tabs__link--active {
        background: transparent;
        color: var(--sw-tab-active);
    }

    .nav-tabs__link--active::before {
        opacity: 1;
        transform: scale(1);
        background: var(--sw-tab-surface, transparent);
        border: 1px solid
            color-mix(in srgb, var(--sw-tab-active) 10%, transparent);
    }
}

@media (max-width: 640px) {
    .nav-tabs__trigger {
        min-height: 3rem;
        border-radius: var(--sw-radius-lg);
        padding-inline: var(--sw-space-sm);
    }

    .nav-tabs__link {
        min-height: 3rem;
        padding-inline: 1rem;
    }
}

@media (min-width: 960px) and (hover: hover) {
    .nav-tabs__link:hover {
        color: var(--sw-tab-active);
    }

    .nav-tabs__link:hover::before {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes nav-tabs-phone-ring {
    0%,
    82%,
    100% {
        transform: translateY(-1px) rotate(0deg);
    }

    86% {
        transform: translateY(-1px) rotate(16deg);
    }

    90% {
        transform: translateY(-1px) rotate(-12deg);
    }

    94% {
        transform: translateY(-1px) rotate(10deg);
    }
}

:global(html[data-motion='reduced'] .nav-tabs__link-meta--contact::after) {
    animation: none;
}
</style>
