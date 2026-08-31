<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { copy as copyTree } from '@/copy';
import { isNavPath } from '@/copy/navPath';
import type { SiteProps } from '@/types';

type NavItem = {
    label: string;
    href: string;
};

const props = defineProps<{
    items: NavItem[];
    currentUrl: string;
}>();

const page = usePage<{ site: SiteProps }>();
const menuId = 'primary-navigation';
const mobileMenuOpen = ref(false);
const isDesktopViewport = ref(false);
const pendingMobileHref = ref<string | null>(null);

let desktopMediaQuery: MediaQueryList | null = null;
let optimisticResetTimer: number | undefined;

function normalizePath(value: string): string {
    if (!value) {
        return '/';
    }

    let pathname = value;

    try {
        pathname = new URL(value, 'http://sidewalk.local').pathname;
    } catch {
        pathname = value;
    }

    if (!pathname.startsWith('/')) {
        pathname = `/${pathname}`;
    }

    pathname = pathname.replace(/\/+$/, '');

    return pathname === '' ? '/' : pathname;
}

function stripLocalePrefix(path: string): string {
    const normalized = normalizePath(path);
    const localePrefix = normalizePath(`/${page.props.site.locale}`);

    if (normalized === localePrefix) {
        return '/';
    }

    if (normalized.startsWith(`${localePrefix}/`)) {
        return normalized.slice(localePrefix.length) || '/';
    }

    return normalized;
}

function matchesPath(currentPath: string, href: string): boolean {
    const itemPath = normalizePath(href);
    const unlocalizedItemPath = stripLocalePrefix(itemPath);

    if (unlocalizedItemPath === '/') {
        return currentPath === itemPath;
    }

    return currentPath === itemPath || currentPath.startsWith(`${itemPath}/`);
}

const resolvedDesktopPath = computed(() => normalizePath(props.currentUrl));
const resolvedMobilePath = computed(() =>
    normalizePath(pendingMobileHref.value ?? props.currentUrl),
);

function isDesktopActive(item: NavItem): boolean {
    return matchesPath(resolvedDesktopPath.value, item.href);
}

const activeItem = computed(
    () =>
        props.items.find((item) =>
            matchesPath(
                isDesktopViewport.value
                    ? resolvedDesktopPath.value
                    : resolvedMobilePath.value,
                item.href,
            ),
        ) ??
        props.items[0] ??
        null,
);
const panelItems = computed(() =>
    isDesktopViewport.value
        ? props.items
        : props.items.filter(
              (item) => !matchesPath(resolvedMobilePath.value, item.href),
          ),
);

function linkAction(item: NavItem): string {
    const itemPath = stripLocalePrefix(item.href);

    return isNavPath(itemPath)
        ? copyTree[page.props.site.locale].layout.navigation.action[itemPath]
        : page.props.site.shell.navOpenLabel;
}

function isContact(item: NavItem): boolean {
    return stripLocalePrefix(item.href) === '/contact';
}

function closeMenu(): void {
    mobileMenuOpen.value = false;
}

function clearOptimisticTimer(): void {
    if (optimisticResetTimer !== undefined) {
        window.clearTimeout(optimisticResetTimer);
        optimisticResetTimer = undefined;
    }
}

function handleLinkClick(item: NavItem): void {
    if (!isDesktopViewport.value) {
        pendingMobileHref.value = item.href;
    }

    closeMenu();
    clearOptimisticTimer();

    optimisticResetTimer = window.setTimeout(() => {
        pendingMobileHref.value = null;
    }, 2000);
}

function toggleMenu(): void {
    mobileMenuOpen.value = !mobileMenuOpen.value;
}

function handleViewportChange(event: MediaQueryListEvent): void {
    isDesktopViewport.value = event.matches;

    if (event.matches) {
        closeMenu();
    }
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closeMenu();
    }
}

watch(
    () => props.currentUrl,
    () => {
        pendingMobileHref.value = null;
        clearOptimisticTimer();
        closeMenu();
    },
);

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    desktopMediaQuery = window.matchMedia('(min-width: 960px)');
    isDesktopViewport.value = desktopMediaQuery.matches;
    desktopMediaQuery.addEventListener('change', handleViewportChange);
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    desktopMediaQuery?.removeEventListener('change', handleViewportChange);
    clearOptimisticTimer();

    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', handleKeydown);
    }
});
</script>

<template>
    <nav class="nav-tabs" :aria-label="page.props.site.shell.navAriaLabel">
        <button
            type="button"
            class="nav-tabs__trigger"
            :class="{ 'nav-tabs__trigger--open': mobileMenuOpen }"
            :aria-controls="menuId"
            :aria-expanded="mobileMenuOpen"
            @click="toggleMenu"
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
            class="nav-tabs__viewport"
            :class="{ 'nav-tabs__viewport--open': mobileMenuOpen }"
        >
            <div v-if="panelItems.length" :id="menuId" class="nav-tabs__panel">
                <Link
                    v-for="item in panelItems"
                    :key="item.href"
                    :href="item.href"
                    class="nav-tabs__link"
                    :class="{
                        'nav-tabs__link--active':
                            isDesktopViewport && isDesktopActive(item),
                    }"
                    @click="handleLinkClick(item)"
                >
                    <span class="nav-tabs__link-label">{{ item.label }}</span>
                    <span
                        class="nav-tabs__link-meta"
                        :class="{
                            'nav-tabs__link-meta--contact': isContact(item),
                        }"
                    >
                        <span>{{ linkAction(item) }}</span>
                    </span>
                </Link>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.nav-tabs {
    position: relative;
    display: grid;
    gap: 6px;
    width: 100%;
    max-width: 380px;
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
        border-color 90ms ease,
        background-color 90ms ease,
        color 90ms ease;
}

.nav-tabs__trigger-copy {
    display: grid;
    gap: 2px;
    padding-left: 4px;
    text-align: left;
    opacity: 1;
}

.nav-tabs__trigger-label {
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
    opacity: 1;
}

.nav-tabs__trigger-current {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.15;
    opacity: 1;
}

.nav-tabs__trigger-icon {
    position: relative;
    width: 18px;
    height: 14px;
    flex: none;
    opacity: 1;
}

.nav-tabs__trigger-icon span {
    position: absolute;
    left: 0;
    width: 100%;
    height: 2px;
    border-radius: 9999px;
    background: currentColor;
    transition:
        transform 90ms ease,
        opacity 90ms ease;
}

.nav-tabs__trigger-icon span:first-child {
    top: 3px;
}

.nav-tabs__trigger-icon span:last-child {
    bottom: 3px;
}

.nav-tabs__trigger--open .nav-tabs__trigger-icon span:first-child {
    transform: translateY(3px) rotate(45deg);
}

.nav-tabs__trigger--open .nav-tabs__trigger-icon span:last-child {
    transform: translateY(-3px) rotate(-45deg);
}

.nav-tabs__trigger--open {
    border-color: color-mix(in srgb, var(--sw-border) 92%, transparent);
    background: var(--sw-bg-grid);
    color: var(--sw-text-primary);
}

.nav-tabs__viewport {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: auto;
    width: 100%;
    min-width: 100%;
    max-height: 0;
    opacity: 1;
    visibility: hidden;
    pointer-events: none;
    overflow: hidden;
    z-index: 6;
    transition: max-height 140ms ease;
}

.nav-tabs__viewport--open {
    max-height: 22rem;
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}

.nav-tabs__panel {
    display: grid;
    gap: 10px;
    width: 100%;
    padding: 12px;
    border-radius: calc(var(--sw-radius-lg) + 2px);
    border: 1px solid color-mix(in srgb, var(--sw-border) 92%, transparent);
    background: var(--sw-bg-base);
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
        background-color 90ms ease,
        border-color 90ms ease,
        color 90ms ease;
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
        opacity 120ms ease,
        transform 150ms ease;
}

.nav-tabs__link::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    opacity: 0;
    background: color-mix(in srgb, var(--sw-bg-base) 82%, var(--sw-bg-grid));
    transition: opacity 120ms ease;
}

.nav-tabs__link > * {
    position: relative;
    z-index: 1;
    opacity: 1;
}

.nav-tabs__link-label {
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
    opacity: 1;
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
    opacity: 1;
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

    .nav-tabs__viewport {
        display: block;
        position: static;
        max-height: none;
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        overflow: visible;
        z-index: auto;
    }

    .nav-tabs__panel,
    .nav-tabs__viewport--open .nav-tabs__panel {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: var(--sw-space-2xs);
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        -webkit-backdrop-filter: none;
        backdrop-filter: none;
        overflow: visible;
    }

    .nav-tabs__link {
        position: relative;
        isolation: isolate;
        min-height: 0;
        width: auto;
        border: 0;
        border-radius: 3px;
        background: transparent;
        padding: calc(var(--sw-space-4xs) + 2px) var(--sw-space-xs);
        font-family: var(--sw-font-heading);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sw-tab-inactive);
        box-shadow: none;
        -webkit-backdrop-filter: none;
        backdrop-filter: none;
        opacity: 1;
        transform: none;
        overflow: hidden;
        transition: color 120ms ease-out;
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
        will-change: opacity, transform;
        transition:
            opacity 70ms linear,
            transform 150ms ease-out,
            background-color 120ms ease-out;
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
        box-shadow: none;
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

    .nav-tabs__viewport {
        top: calc(100% + 4px);
    }

    .nav-tabs__panel,
    .nav-tabs__viewport--open .nav-tabs__panel {
        padding: 12px;
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
