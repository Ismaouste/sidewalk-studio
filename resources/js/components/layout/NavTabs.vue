<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
const optimisticActiveHref = ref<string | null>(null);

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

const currentPath = computed(() =>
    normalizePath(optimisticActiveHref.value ?? props.currentUrl),
);

function isActive(item: NavItem): boolean {
    const resolvedCurrentPath = currentPath.value;
    const itemPath = normalizePath(item.href);

    if (itemPath === '/') {
        return !props.items.some((navItem) => {
            const navPath = normalizePath(navItem.href);

            if (navPath === '/') {
                return false;
            }

            return (
                resolvedCurrentPath === navPath ||
                resolvedCurrentPath.endsWith(navPath) ||
                resolvedCurrentPath.includes(`${navPath}/`)
            );
        });
    }

    return (
        resolvedCurrentPath === itemPath ||
        resolvedCurrentPath.endsWith(itemPath) ||
        resolvedCurrentPath.includes(`${itemPath}/`)
    );
}

const activeItem = computed(
    () => props.items.find((item) => isActive(item)) ?? props.items[0] ?? null,
);
const inactiveItems = computed(() =>
    props.items.filter((item) => !isActive(item)),
);
const panelItems = computed(() =>
    isDesktopViewport.value ? props.items : inactiveItems.value,
);

function linkAction(item: NavItem): string {
    if (page.props.site.locale === 'fr') {
        return (
            {
                '/': 'Commencer la visite',
                '/projects': 'Lire plus',
                '/journal': 'Découvrir',
                '/contact': 'Échanger',
            }[item.href] ?? page.props.site.shell.navOpenLabel
        );
    }

    return (
        {
            '/': 'Start the visit',
            '/projects': 'Read more',
            '/journal': 'Discover',
            '/contact': 'Reach out',
        }[item.href] ?? page.props.site.shell.navOpenLabel
    );
}

function isContact(item: NavItem): boolean {
    return item.href === '/contact';
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
    optimisticActiveHref.value = item.href;
    closeMenu();
    clearOptimisticTimer();

    optimisticResetTimer = window.setTimeout(() => {
        optimisticActiveHref.value = null;
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
        optimisticActiveHref.value = null;
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
            <div
                v-if="panelItems.length"
                :id="menuId"
                class="nav-tabs__panel"
            >
                <Link
                    v-for="item in panelItems"
                    :key="item.href"
                    :href="item.href"
                    class="nav-tabs__link"
                    :class="{ 'nav-tabs__link--active': isActive(item) }"
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
                        <span
                            class="nav-tabs__link-arrow"
                            :class="{
                                'nav-tabs__link-arrow--contact': isContact(item),
                            }"
                            aria-hidden="true"
                        >
                            →
                        </span>
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
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-surface) 84%, transparent);
    padding-inline: 1rem;
    color: var(--sw-text-primary);
    box-shadow: var(--sw-shadow-sm);
    transition:
        border-color 90ms ease,
        background-color 90ms ease,
        box-shadow 90ms ease,
        color 90ms ease;
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
    border-radius: var(--sw-radius-full);
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
    background: color-mix(in srgb, var(--sw-bg-grid) 82%, transparent);
    color: color-mix(in srgb, var(--sw-text-secondary) 88%, var(--sw-text-primary));
    box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--sw-border) 56%, transparent);
}

.nav-tabs__viewport {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: auto;
    width: 100%;
    min-width: 100%;
    max-height: 0;
    opacity: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 6;
    transition:
        max-height 140ms ease,
        opacity 90ms ease;
}

.nav-tabs__viewport--open {
    max-height: 22rem;
    opacity: 1;
    pointer-events: auto;
}

.nav-tabs__panel {
    display: grid;
    gap: 10px;
    width: 100%;
    padding: 12px;
}

.nav-tabs__link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 3.25rem;
    gap: var(--sw-space-xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(
        in srgb,
        var(--sw-bg-elevated) 86%,
        var(--sw-bg-surface)
    );
    padding: 0.9rem 1rem;
    color: var(--sw-text-primary);
    box-shadow:
        0 14px 22px color-mix(in srgb, var(--sw-text-primary) 8%, transparent),
        var(--sw-shadow-sm);
    opacity: 0;
    transform: translateY(-6px);
    transition:
        background-color 90ms ease,
        border-color 90ms ease,
        box-shadow 90ms ease,
        color 90ms ease,
        opacity 140ms ease,
        transform 140ms ease;
}

.nav-tabs__viewport--open .nav-tabs__link {
    opacity: 1;
    transform: translateY(0);
}

.nav-tabs__viewport--open .nav-tabs__link:nth-child(1) {
    transition-delay: 10ms;
}

.nav-tabs__viewport--open .nav-tabs__link:nth-child(2) {
    transition-delay: 20ms;
}

.nav-tabs__viewport--open .nav-tabs__link:nth-child(3) {
    transition-delay: 30ms;
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

.nav-tabs__link-arrow {
    display: inline-block;
    font-size: 12px;
    line-height: 1;
    transform: translateY(-1px);
}

.nav-tabs__link-arrow--contact {
    transform-origin: center;
    animation: nav-tabs-phone-ring 1.6s ease-in-out infinite;
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
    box-shadow: var(--sw-shadow-md);
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
        background: color-mix(in srgb, var(--sw-bg-elevated) 76%, transparent);
    }

    .nav-tabs__link:hover {
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 24%,
            var(--sw-border)
        );
        background: color-mix(in srgb, var(--sw-bg-elevated) 80%, transparent);
        color: var(--sw-text-primary);
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
        border-radius: var(--sw-radius-full);
        background: transparent;
        padding: calc(var(--sw-space-4xs) + 2px) var(--sw-space-xs);
        font-family: var(--sw-font-heading);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sw-tab-inactive);
        box-shadow: none;
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
        box-shadow: inset 0 0 0 1px
            color-mix(in srgb, var(--sw-tab-active) 10%, transparent);
    }
}

@media (max-width: 640px) {
    .nav-tabs__trigger {
        min-height: 3rem;
        border-radius: var(--sw-radius-full);
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
</style>
