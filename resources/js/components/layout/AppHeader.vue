<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SunAnchor from '@/components/design-system/SunAnchor.vue';
import LocaleSwitcher from '@/components/layout/LocaleSwitcher.vue';
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

                    <div class="app-header__utilities">
                        <LocaleSwitcher />
                        <ThemeToggle />
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.app-header {
    position: sticky;
    top: max(var(--sw-space-3xs), env(safe-area-inset-top));
    z-index: 30;
    padding-top: max(var(--sw-space-3xs), env(safe-area-inset-top));
}

.app-header__inner {
    position: relative;
    display: grid;
    gap: clamp(var(--sw-space-xs), 2.8vw, var(--sw-space-sm));
    overflow: visible;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: calc(var(--sw-radius-lg) + 4px);
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-elevated) 36%, transparent),
            transparent 58%
        ),
        var(--sw-header-bg);
    padding: clamp(14px, 2vw, var(--sw-space-sm));
    box-shadow: var(--sw-shadow-md);
    backdrop-filter: blur(18px);
}

.app-header__inner::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 1px 0 color-mix(in srgb, white 22%, transparent);
    pointer-events: none;
}

.app-header__brand-wrap,
.app-header__controls {
    position: relative;
    z-index: 1;
}

.app-header__brand {
    display: inline-grid;
    gap: 6px;
    max-width: 30rem;
}

.app-header__name {
    color: var(--sw-text-primary);
}

.app-header__tagline {
    font-family: var(--sw-font-body);
    font-size: clamp(13px, 2.8vw, 15px);
    font-weight: 500;
    line-height: 1.4;
    color: color-mix(
        in srgb,
        var(--sw-text-primary) 80%,
        var(--sw-text-secondary)
    );
}

.app-header__controls {
    display: grid;
    gap: var(--sw-space-3xs);
}

.app-header__utilities {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
    align-items: center;
}

.app-header__controls :deep(.nav-tabs) {
    min-width: 0;
}

.app-header__controls :deep(.locale-switcher),
.app-header__controls :deep(.theme-toggle) {
    justify-self: start;
}

@media (min-width: 960px) {
    .app-header__inner {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .app-header__controls {
        justify-items: end;
        gap: var(--sw-space-xs);
    }

    .app-header__utilities {
        justify-content: flex-end;
    }

    .app-header__controls :deep(.locale-switcher),
    .app-header__controls :deep(.theme-toggle) {
        justify-self: end;
    }
}
</style>
