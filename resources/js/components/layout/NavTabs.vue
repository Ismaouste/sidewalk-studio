<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

type NavItem = {
    label: string;
    href: string;
};

const props = defineProps<{
    items: NavItem[];
    currentUrl: string;
}>();

const menuId = 'primary-navigation';
const mobileMenuOpen = ref(false);

let desktopMediaQuery: MediaQueryList | null = null;

function isActive(item: NavItem): boolean {
    if (item.href === '/') {
        return props.currentUrl === '/';
    }

    return props.currentUrl.startsWith(item.href);
}

const activeItem = computed(
    () => props.items.find((item) => isActive(item)) ?? props.items[0] ?? null,
);

function closeMenu(): void {
    mobileMenuOpen.value = false;
}

function toggleMenu(): void {
    mobileMenuOpen.value = !mobileMenuOpen.value;
}

function handleViewportChange(event: MediaQueryListEvent): void {
    if (event.matches) {
        closeMenu();
    }
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closeMenu();
    }
}

watch(() => props.currentUrl, closeMenu);

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    desktopMediaQuery = window.matchMedia('(min-width: 960px)');
    desktopMediaQuery.addEventListener('change', handleViewportChange);
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    desktopMediaQuery?.removeEventListener('change', handleViewportChange);

    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', handleKeydown);
    }
});
</script>

<template>
    <nav class="nav-tabs" aria-label="Primary navigation">
        <button
            type="button"
            class="nav-tabs__trigger"
            :class="{ 'nav-tabs__trigger--open': mobileMenuOpen }"
            :aria-controls="menuId"
            :aria-expanded="mobileMenuOpen"
            @click="toggleMenu"
        >
            <span class="nav-tabs__trigger-copy">
                <span class="nav-tabs__trigger-label">Menu</span>
                <span class="nav-tabs__trigger-current">
                    {{ activeItem?.label ?? 'Navigation' }}
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
            <div :id="menuId" class="nav-tabs__panel">
                <Link
                    v-for="item in props.items"
                    :key="item.href"
                    :href="item.href"
                    class="nav-tabs__link"
                    :class="{ 'nav-tabs__link--active': isActive(item) }"
                    :aria-current="isActive(item) ? 'page' : undefined"
                    @click="closeMenu"
                >
                    <span class="nav-tabs__link-label">{{ item.label }}</span>
                    <span class="nav-tabs__link-meta">
                        {{ isActive(item) ? 'Current' : 'Open' }}
                    </span>
                </Link>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.nav-tabs {
    display: grid;
    gap: var(--sw-space-3xs);
}

.nav-tabs__trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 3.25rem;
    gap: var(--sw-space-xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 86%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-surface) 84%, transparent);
    padding-inline: 1rem;
    color: var(--sw-text-primary);
    box-shadow: var(--sw-shadow-sm);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.nav-tabs__trigger-copy {
    display: grid;
    gap: 2px;
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
        transform var(--sw-motion-fast),
        opacity var(--sw-motion-fast);
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

.nav-tabs__viewport {
    display: grid;
    grid-template-rows: 0fr;
    opacity: 0;
    pointer-events: none;
    transition:
        grid-template-rows var(--sw-motion-smooth),
        opacity var(--sw-motion-fast);
}

.nav-tabs__viewport--open {
    grid-template-rows: 1fr;
    opacity: 1;
    pointer-events: auto;
}

.nav-tabs__panel {
    display: grid;
    gap: var(--sw-space-3xs);
    min-height: 0;
    overflow: hidden;
}

.nav-tabs__viewport--open .nav-tabs__panel {
    padding-top: var(--sw-space-2xs);
}

.nav-tabs__link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 3rem;
    gap: var(--sw-space-xs);
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: calc(var(--sw-radius-md) + 2px);
    background: color-mix(in srgb, var(--sw-bg-surface) 82%, transparent);
    padding: 0.85rem 1rem;
    color: var(--sw-text-secondary);
    box-shadow: var(--sw-shadow-sm);
    transition:
        background-color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast),
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.nav-tabs__link-label {
    font-family: var(--sw-font-body);
    font-size: 15px;
    font-weight: 600;
    line-height: 1.2;
}

.nav-tabs__link-meta {
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-accent-sun);
}

.nav-tabs__link--active {
    border-color: color-mix(
        in srgb,
        var(--sw-accent-dominant) 22%,
        var(--sw-border)
    );
    background: var(--sw-bg-elevated);
    color: var(--sw-text-primary);
    box-shadow: var(--sw-shadow-md);
}

.nav-tabs__link:active,
.nav-tabs__trigger:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .nav-tabs__trigger:hover,
    .nav-tabs__link:hover {
        transform: translateY(-1px);
    }

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
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: var(--sw-space-2xs);
    }

    .nav-tabs__trigger {
        display: none;
    }

    .nav-tabs__viewport {
        display: block;
        opacity: 1;
        pointer-events: auto;
    }

    .nav-tabs__panel,
    .nav-tabs__viewport--open .nav-tabs__panel {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: var(--sw-space-2xs);
        padding-top: 0;
        overflow: visible;
    }

    .nav-tabs__link {
        position: relative;
        min-height: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        padding: var(--sw-space-3xs) 0;
        font-family: var(--sw-font-heading);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sw-tab-inactive);
        box-shadow: none;
    }

    .nav-tabs__link::after {
        content: '';
        position: absolute;
        right: 0;
        bottom: -4px;
        left: 0;
        height: 2px;
        transform: scaleX(0);
        transform-origin: center;
        background: var(--sw-tab-line);
        transition: transform var(--sw-motion-fast);
    }

    .nav-tabs__link-label {
        font-family: inherit;
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
    }

    .nav-tabs__link-meta {
        display: none;
    }

    .nav-tabs__link--active {
        background: transparent;
        color: var(--sw-tab-active);
    }

    .nav-tabs__link--active::after {
        transform: scaleX(1);
    }
}

@media (min-width: 960px) and (hover: hover) {
    .nav-tabs__link:hover {
        background: transparent;
        border-color: transparent;
        color: var(--sw-tab-active);
    }
}
</style>
