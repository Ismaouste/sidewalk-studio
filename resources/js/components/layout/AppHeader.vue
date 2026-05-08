<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import BrandMark from '@/components/branding/BrandMark.vue';
import NavTabs from '@/components/layout/NavTabs.vue';
import {
    localizePublicHref,
    resolvePublicHref,
    sanitizePublicHref,
} from '@/lib/publicHref';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const navigation = computed(() => page.props.site.navigation);
const brandName = 'Ismaël Rodmacq';
const staticLocationPath = ref<string | null>(null);

function normalizePreviewPath(
    pathname: string,
    basePath?: string | null,
): string {
    const base = (basePath ?? '').trim();

    if (!base || base === '/') {
        return pathname || '/';
    }

    const normalizedBase = `/${base.replace(/^\/+|\/+$/g, '')}`;
    let path = pathname;

    if (path.startsWith(normalizedBase)) {
        path = path.slice(normalizedBase.length) || '/';
    }

    if (path.endsWith('/index.html')) {
        path = path.slice(0, -'/index.html'.length) || '/';
    }

    return path === '' ? '/' : path;
}

const currentUrl = computed(() => {
    if (
        page.props.site.runtime.staticPreview &&
        staticLocationPath.value !== null
    ) {
        return sanitizePublicHref(
            normalizePreviewPath(
                staticLocationPath.value,
                page.props.site.runtime.staticBasePath,
            ),
        );
    }

    return sanitizePublicHref(page.url);
});
const homeHref = computed(() =>
    resolvePublicHref(
        localizePublicHref('/', page.props.site.locale),
        page.props.site.runtime.staticPreview,
        page.props.site.runtime.staticBasePath,
    ),
);
const branding = computed(() => {
    const settings = { ...page.props.site.branding };

    if (settings.uploaded_asset_path) {
        settings.uploaded_asset_path = resolvePublicHref(
            settings.uploaded_asset_path,
            page.props.site.runtime.staticPreview,
            page.props.site.runtime.staticBasePath,
        );
    }

    return settings;
});
const headerRef = ref<HTMLElement | null>(null);

let resizeObserver: ResizeObserver | null = null;

function syncHeaderHeight(): void {
    if (typeof window === 'undefined' || !headerRef.value) {
        return;
    }

    const height = Math.ceil(headerRef.value.getBoundingClientRect().height);
    document.documentElement.style.setProperty(
        '--sw-public-header-height',
        `${height}px`,
    );
}

onMounted(() => {
    if (typeof window !== 'undefined') {
        staticLocationPath.value = sanitizePublicHref(
            `${window.location.pathname}${window.location.search}`,
        );
    }

    syncHeaderHeight();

    if (typeof window === 'undefined') {
        return;
    }

    window.addEventListener('resize', syncHeaderHeight, { passive: true });

    if ('ResizeObserver' in window && headerRef.value) {
        resizeObserver = new ResizeObserver(() => {
            syncHeaderHeight();
        });
        resizeObserver.observe(headerRef.value);
    }
});

onBeforeUnmount(() => {
    resizeObserver?.disconnect();

    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', syncHeaderHeight);
    }
});
</script>

<template>
    <header ref="headerRef" class="app-header">
        <div class="app-header__shell">
            <div class="app-header__inner">
                <div class="app-header__topline">
                    <Link :href="homeHref" class="app-header__brand">
                        <BrandMark
                            :branding="branding"
                            class-name="app-header__avatar"
                        />
                        <span class="app-header__identity">
                            <span class="type-eyebrow app-header__name">
                                {{ brandName }}
                            </span>
                            <span class="app-header__tagline">
                                {{ page.props.site.shell.headerTagline }}
                            </span>
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
    top: env(safe-area-inset-top);
    z-index: var(--sw-z-header);
    isolation: isolate;
    padding-top: max(clamp(10px, 1.4vw, 16px), env(safe-area-inset-top));
}

.app-header::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: calc(100% + clamp(40px, 8vw, 96px));
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--sw-bg-base) 98%, transparent) 0%,
        color-mix(in srgb, var(--sw-bg-base) 90%, transparent) 34%,
        color-mix(in srgb, var(--sw-bg-base) 52%, transparent) 68%,
        color-mix(in srgb, var(--sw-bg-base) 16%, transparent) 88%,
        transparent 100%
    );
    pointer-events: none;
    z-index: 0;
}

:global(html[data-theme='sunset']) .app-header::before {
    background:
        radial-gradient(
            circle at 14% 0%,
            color-mix(in srgb, var(--sw-accent-sun) 22%, transparent),
            transparent 34%
        ),
        radial-gradient(
            circle at 82% 14%,
            color-mix(in srgb, var(--sw-accent-violet) 20%, transparent),
            transparent 42%
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-base) 96%, transparent) 0%,
            color-mix(in srgb, var(--sw-bg-base) 84%, transparent) 28%,
            color-mix(in srgb, var(--sw-bg-base) 48%, transparent) 66%,
            transparent 100%
        );
}

.app-header__shell {
    width: min(
        calc(100% - var(--sw-layout-gutter-md)),
        calc(var(--sw-shell-max-width) + 112px)
    );
    margin-inline: auto;
    position: relative;
    z-index: 1;
}

.app-header__inner {
    position: relative;
    display: grid;
    gap: clamp(10px, 2vw, var(--sw-space-xs));
    overflow: visible;
    border: 1px solid color-mix(in srgb, var(--sw-border) 84%, transparent);
    border-radius: var(--sw-radius-lg);
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
    -webkit-backdrop-filter: var(--sw-surface-backdrop-filter);
    backdrop-filter: var(--sw-surface-backdrop-filter);
}

:global(html[data-theme='sunset']) .app-header__inner {
    border-color: color-mix(
        in srgb,
        var(--sw-accent-violet) 18%,
        var(--sw-border)
    );
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-elevated) 36%, transparent),
            transparent 60%
        ),
        linear-gradient(
            135deg,
            color-mix(
                in srgb,
                var(--sw-bg-surface) 80%,
                var(--sw-accent-violet) 20%
            ),
            color-mix(
                in srgb,
                var(--sw-bg-elevated) 78%,
                var(--sw-accent-sun) 22%
            )
        ),
        radial-gradient(
            circle at 14% 0%,
            color-mix(in srgb, var(--sw-accent-coral) 12%, transparent),
            transparent 34%
        ),
        radial-gradient(
            circle at 88% 120%,
            color-mix(in srgb, var(--sw-accent-violet) 16%, transparent),
            transparent 46%
        );
}

.app-header__inner::before {
    content: '';
    position: absolute;
    inset: -20px -56px -12px -56px;
    pointer-events: none;
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
        var(--sw-sun-opacity-global, 0.68) * var(--sw-header-glow-opacity, 1)
    );
    pointer-events: none;
}

:global(html[data-theme='sunset']) .app-header__inner::before {
    inset: -34px -72px -20px -72px;
    background:
        radial-gradient(
            circle at var(--sw-sun-vx, 78%) var(--sw-sun-vy, 18%),
            color-mix(in srgb, var(--sw-accent-sun) 46%, transparent),
            color-mix(in srgb, var(--sw-accent-coral) 24%, transparent) 18%,
            color-mix(in srgb, var(--sw-accent-violet) 18%, transparent) 36%,
            transparent 54%
        ),
        radial-gradient(
            circle at 22% 110%,
            color-mix(in srgb, var(--sw-accent-violet) 22%, transparent),
            transparent 48%
        ),
        linear-gradient(
            118deg,
            color-mix(in srgb, var(--sw-accent-violet) 12%, transparent),
            color-mix(in srgb, var(--sw-accent-sun) 12%, transparent) 34%,
            transparent 66%
        );
    background-size:
        160% 160%,
        180% 180%,
        170% 170%;
    background-position:
        0% 0%,
        100% 100%,
        18% 24%;
}

.app-header__inner::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    border: 1px solid color-mix(in srgb, white 8%, transparent);
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
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
    column-gap: 12px;
    max-width: 34rem;
}

.app-header__identity {
    display: inline-grid;
    gap: 4px;
    min-width: 0;
}

.app-header__avatar {
    display: block;
    flex: 0 0 auto;
    inline-size: 36px;
    block-size: 36px;
    border-radius: 9999px;
    object-fit: cover;
}

.app-header__name {
    font-size: clamp(12px, 1.25vw, 14px);
    font-weight: 700;
    letter-spacing: 0.08em;
    color: color-mix(in srgb, var(--sw-text-primary) 90%, transparent);
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

@media (max-width: 1024px) {
    .app-header__shell {
        width: min(
            calc(100% - var(--sw-layout-gutter-sm)),
            calc(var(--sw-shell-max-width) + 72px)
        );
    }
}

@media (max-width: 640px) {
    .app-header {
        top: 0;
        padding-top: 0;
    }

    .app-header::before {
        display: none;
    }

    .app-header__shell {
        width: 100%;
    }

    .app-header__inner {
        border-radius: 0;
        border-inline: 0;
        padding-inline: var(--sw-space-sm);
        background: color-mix(in srgb, var(--sw-bg-base) 94%, transparent);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
    }

    .app-header__inner::before {
        display: none;
    }

    .app-header__inner::after {
        border-radius: 0;
    }

    :global(html[data-theme='sunset']) .app-header__inner {
        background: color-mix(in srgb, var(--sw-bg-base) 94%, transparent);
    }

    .app-header__identity {
        gap: 3px;
    }

    .app-header__avatar {
        inline-size: 32px;
        block-size: 32px;
    }
}
</style>
