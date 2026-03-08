<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import LocaleSwitcher from '@/components/layout/LocaleSwitcher.vue';
import NavTabs from '@/components/layout/NavTabs.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const navigation = computed(() => page.props.site.navigation);
const currentUrl = computed(() => page.url);
</script>

<template>
    <header class="app-header">
        <div class="sw-container">
            <div class="app-header__inner">
                <div class="app-header__topline">
                    <Link href="/" class="app-header__brand">
                        <span class="type-eyebrow app-header__name">
                            {{ page.props.site.name }}
                        </span>
                        <span class="app-header__tagline">
                            {{ page.props.site.shell.headerTagline }}
                        </span>
                    </Link>

                    <LocaleSwitcher />
                </div>

                <div class="app-header__controls">
                    <NavTabs :items="navigation" :current-url="currentUrl" />
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
    gap: clamp(10px, 2vw, var(--sw-space-xs));
    overflow: visible;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: calc(var(--sw-radius-lg) + 4px);
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-elevated) 24%, transparent),
            transparent 58%
        ),
        linear-gradient(
            160deg,
            color-mix(in srgb, var(--sw-bg-surface) 52%, transparent),
            color-mix(in srgb, var(--sw-bg-elevated) 36%, transparent)
        );
    padding: clamp(10px, 1.8vw, var(--sw-space-xs));
    box-shadow:
        0 14px 42px color-mix(in srgb, var(--sw-text-primary) 12%, transparent),
        var(--sw-shadow-md);
    -webkit-backdrop-filter: blur(30px) saturate(150%);
    backdrop-filter: blur(30px) saturate(150%);
}

.app-header__inner::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: inherit;
    background:
        radial-gradient(
            circle at var(--sw-sun-vx, 14%) var(--sw-sun-vy, 10%),
            color-mix(in srgb, var(--sw-accent-sun) 22%, transparent),
            transparent 34%
        ),
        linear-gradient(
            var(--sw-sun-ray-angle, 32deg),
            color-mix(in srgb, var(--sw-accent-dominant) 12%, transparent),
            transparent 58%
        );
    filter: blur(calc(var(--sw-sun-blur-global, 120px) * 0.24));
    opacity: calc(var(--sw-sun-opacity-global, 0.68) * 1.08);
    pointer-events: none;
}

.app-header__inner::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 1px 0 color-mix(in srgb, white 22%, transparent);
    pointer-events: none;
}

.app-header__topline,
.app-header__controls {
    position: relative;
    z-index: 1;
}

.app-header__topline {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: var(--sw-space-xs);
}

.app-header__brand {
    display: inline-grid;
    gap: 4px;
    max-width: 30rem;
}

.app-header__name {
    color: var(--sw-text-primary);
}

.app-header__tagline {
    font-family: var(--sw-font-body);
    font-size: clamp(12px, 2.6vw, 14px);
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
}

.app-header__controls :deep(.nav-tabs) {
    min-width: 0;
}

.app-header__topline :deep(.locale-switcher) {
    flex: none;
}

@media (min-width: 960px) {
    .app-header__inner {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
    }

    .app-header__topline {
        align-items: end;
    }

    .app-header__controls {
        justify-items: end;
    }
}
</style>
