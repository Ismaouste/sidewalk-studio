<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type NavItem = {
    label: string;
    href: string;
};

const props = defineProps<{
    items: NavItem[];
    currentUrl: string;
}>();

function isActive(item: NavItem): boolean {
    if (item.href === '/') {
        return props.currentUrl === '/';
    }

    return props.currentUrl.startsWith(item.href);
}
</script>

<template>
    <nav class="nav-tabs" aria-label="Primary navigation">
        <Link
            v-for="item in props.items"
            :key="item.href"
            :href="item.href"
            class="nav-tabs__link"
            :class="{ 'nav-tabs__link--active': isActive(item) }"
            :aria-current="isActive(item) ? 'page' : undefined"
        >
            {{ item.label }}
        </Link>
    </nav>
</template>

<style scoped>
.nav-tabs {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: var(--sw-space-2xs);
}

.nav-tabs__link {
    position: relative;
    padding-block: var(--sw-space-3xs);
    font-family: var(--sw-font-heading);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--sw-tab-inactive);
    transition:
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.nav-tabs__link:hover {
    transform: translateY(-1px);
    color: var(--sw-tab-active);
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

.nav-tabs__link--active {
    color: var(--sw-tab-active);
}

.nav-tabs__link--active::after {
    transform: scaleX(1);
}

@media (max-width: 768px) {
    .nav-tabs {
        justify-content: flex-start;
    }
}
</style>
