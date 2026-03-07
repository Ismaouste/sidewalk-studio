<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SunAnchor from '@/components/design-system/SunAnchor.vue';
import NavTabs from '@/components/layout/NavTabs.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const navigation = computed(() => page.props.site.navigation);
const currentUrl = computed(() => page.url);
</script>

<template>
    <header class="app-header">
        <div class="sw-container">
            <div class="app-header__inner">
                <SunAnchor />

                <div class="app-header__brand-wrap">
                    <Link href="/" class="app-header__brand">
                        <span class="type-eyebrow app-header__name">
                            {{ page.props.site.name }}
                        </span>
                        <span class="app-header__tagline">
                            {{ page.props.site.tagline }}
                        </span>
                    </Link>
                </div>

                <div class="app-header__controls">
                    <NavTabs :items="navigation" :current-url="currentUrl" />
                    <ThemeToggle />
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.app-header {
    position: sticky;
    top: var(--sw-space-xs);
    z-index: 20;
    padding-top: var(--sw-space-xs);
}

.app-header__inner {
    position: relative;
    display: grid;
    gap: var(--sw-space-sm);
    overflow: visible;
    border: 1px solid color-mix(in srgb, var(--sw-border) 80%, transparent);
    border-radius: var(--sw-radius-lg);
    background: var(--sw-header-bg);
    padding: var(--sw-space-sm);
    box-shadow: var(--sw-shadow-md);
}

.app-header__brand-wrap,
.app-header__controls {
    position: relative;
    z-index: 1;
}

.app-header__brand {
    display: inline-grid;
    gap: var(--sw-space-3xs);
    max-width: 28rem;
}

.app-header__name {
    color: var(--sw-text-primary);
}

.app-header__tagline {
    font-family: var(--sw-font-body);
    font-size: 15px;
    font-weight: 500;
    line-height: 1.4;
    color: var(--sw-text-primary);
}

.app-header__controls {
    display: grid;
    gap: var(--sw-space-xs);
}

@media (min-width: 960px) {
    .app-header__inner {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .app-header__controls {
        justify-items: end;
    }
}
</style>
