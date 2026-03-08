<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavTabs from '@/components/layout/NavTabs.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const navigation = computed(() => page.props.site.navigation);
const currentUrl = computed(() => page.url);
</script>

<template>
    <header class="app-header">
        <div class="app-header__shell">
            <div class="app-header__inner">
                <div class="app-header__topline">
                    <Link href="/" class="app-header__brand">
                        <span class="app-header__identity">
                            <span class="app-header__mark" aria-hidden="true">
                                <span class="app-header__mark-core"></span>
                            </span>
                            <span class="type-eyebrow app-header__name">
                                {{ page.props.site.name }}
                            </span>
                        </span>
                        <span class="app-header__tagline">
                            {{ page.props.site.shell.headerTagline }}
                        </span>
                    </Link>
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
    z-index: var(--sw-z-header);
    padding-top: max(var(--sw-space-3xs), env(safe-area-inset-top));
}

.app-header__shell {
    width: min(
        calc(100% - var(--sw-layout-gutter-lg)),
        calc(var(--sw-shell-max-width) + 112px)
    );
    margin-inline: auto;
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
    inset: -20px -56px -12px -56px;
    border-radius: inherit;
    background:
        radial-gradient(
            circle at var(--sw-sun-vx, 14%) var(--sw-sun-vy, 10%),
            color-mix(in srgb, var(--sw-accent-sun) 30%, transparent),
            color-mix(in srgb, var(--sw-accent-dominant) 10%, transparent) 24%,
            transparent 40%
        ),
        linear-gradient(
            var(--sw-sun-ray-angle, 32deg),
            color-mix(in srgb, var(--sw-accent-dominant) 12%, transparent),
            transparent 58%
        );
    filter: blur(
        calc(
            var(--sw-sun-blur-global, 120px) *
                var(--sw-header-glow-blur-factor, 0.24)
        )
    );
    opacity: calc(
        var(--sw-sun-opacity-global, 0.68) *
            var(--sw-header-glow-opacity, 1)
    );
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
    gap: 6px;
    max-width: 34rem;
}

.app-header__identity {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.app-header__mark {
    position: relative;
    flex: 0 0 auto;
    inline-size: 34px;
    block-size: 34px;
    border: 1px solid color-mix(in srgb, var(--sw-border) 72%, transparent);
    border-radius: 10px;
    background:
        linear-gradient(
            145deg,
            color-mix(in srgb, var(--sw-accent-sun) 16%, transparent),
            color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent)
        ),
        color-mix(in srgb, var(--sw-bg-surface) 74%, transparent);
    box-shadow:
        inset 0 1px 0 color-mix(in srgb, white 18%, transparent),
        0 10px 20px color-mix(in srgb, var(--sw-text-primary) 8%, transparent);
}

.app-header__mark::before,
.app-header__mark::after {
    content: '';
    position: absolute;
    inset: 7px;
    border-radius: 8px;
    pointer-events: none;
}

.app-header__mark::before {
    inset: 8px;
    border: 1px solid color-mix(in srgb, var(--sw-accent-dominant) 24%, transparent);
    clip-path: polygon(0 0, 35% 0, 35% 12%, 65% 12%, 65% 0, 100% 0, 100% 35%, 88% 35%, 88% 65%, 100% 65%, 100% 100%, 65% 100%, 65% 88%, 35% 88%, 35% 100%, 0 100%, 0 65%, 12% 65%, 12% 35%, 0 35%);
}

.app-header__mark::after {
    inset: 6px;
    background:
        linear-gradient(
            180deg,
            transparent 0,
            transparent 46%,
            color-mix(in srgb, var(--sw-accent-dominant) 22%, transparent) 46%,
            color-mix(in srgb, var(--sw-accent-dominant) 22%, transparent) 54%,
            transparent 54%,
            transparent 100%
        ),
        linear-gradient(
            90deg,
            transparent 0,
            transparent 46%,
            color-mix(in srgb, var(--sw-accent-dominant) 18%, transparent) 46%,
            color-mix(in srgb, var(--sw-accent-dominant) 18%, transparent) 54%,
            transparent 54%,
            transparent 100%
        );
    opacity: 0.8;
}

.app-header__mark-core {
    position: absolute;
    inset: 50% auto auto 50%;
    inline-size: 11px;
    block-size: 11px;
    border-radius: 999px;
    transform: translate(-50%, -50%);
    background:
        radial-gradient(
            circle at 35% 35%,
            color-mix(in srgb, white 56%, var(--sw-accent-sun)),
            var(--sw-accent-sun) 62%,
            color-mix(in srgb, var(--sw-accent-coral) 56%, var(--sw-accent-sun))
        );
    box-shadow:
        0 0 0 4px color-mix(in srgb, var(--sw-accent-sun) 16%, transparent),
        0 0 18px color-mix(in srgb, var(--sw-accent-sun) 24%, transparent);
}

.app-header__name {
    font-size: clamp(11px, 1.2vw, 13px);
    font-weight: 800;
    letter-spacing: 0.18em;
    color: var(--sw-text-primary);
}

.app-header__tagline {
    font-family: var(--sw-font-body);
    font-size: clamp(12px, 2.6vw, 14px);
    font-weight: 500;
    line-height: 1.4;
    color: color-mix(in srgb, var(--sw-text-secondary) 78%, white 22%);
}

.app-header__controls {
    display: grid;
}

.app-header__controls :deep(.nav-tabs) {
    min-width: 0;
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

html[data-theme='sunset'] .app-header__mark {
    background:
        linear-gradient(
            145deg,
            color-mix(in srgb, var(--sw-accent-sun) 18%, transparent),
            color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent)
        ),
        color-mix(in srgb, var(--sw-bg-surface) 78%, transparent);
}

html[data-theme='sunset'] .app-header__mark::before {
    border-color: color-mix(in srgb, var(--sw-accent-coral) 28%, transparent);
}

html[data-theme='sunset'] .app-header__mark::after {
    opacity: 0.9;
}

@media (max-width: 1024px) {
    .app-header__shell {
        width: min(
            calc(100% - calc(var(--sw-layout-gutter-md) + 8px)),
            calc(var(--sw-shell-max-width) + 72px)
        );
    }
}

@media (max-width: 640px) {
    .app-header {
        top: 0;
        padding-top: 0;
    }

    .app-header__shell {
        width: 100%;
    }

    .app-header__inner::before {
        inset: -28px -36px -10px -36px;
    }

    .app-header__inner {
        border-radius: 0;
        border-inline: 0;
        padding-inline: var(--sw-space-sm);
    }

    .app-header__inner::after {
        border-radius: 0;
    }

    .app-header__identity {
        gap: 10px;
    }

    .app-header__mark {
        inline-size: 30px;
        block-size: 30px;
        border-radius: 9px;
    }
}
</style>
