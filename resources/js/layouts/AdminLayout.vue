<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminHint from '@/components/admin/shared/AdminHint.vue';
import BrandMark from '@/components/branding/BrandMark.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import QuoteLinePreview from '@/components/shared/QuoteLinePreview.vue';
import type { Auth, FlashProps, LoaderQuote, SiteProps, User } from '@/types';

type AdminPageProps = {
    auth: Auth;
    flash: FlashProps;
    site: SiteProps;
};

const page = usePage<AdminPageProps>();
const navigation = [
    {
        label: 'Publications',
        href: '/admin/publications',
        note: 'Notes, journal entries, case studies, and CTA settings',
    },
    {
        label: 'Pages',
        href: '/admin/pages',
        note: 'SEO and structured payload overrides for public pages',
    },
    {
        label: 'Language files',
        href: '/admin/language-files',
        note: 'Structured editing for repo-backed visible site copy',
    },
    {
        label: 'Theme & publishing',
        href: '/admin/theme',
        note: 'Theme defaults, static export controls, and rebuild state',
    },
    {
        label: 'Branding',
        href: '/admin/branding',
        note: 'Shared public/admin identity asset and fallback variants',
    },
    {
        label: 'Loader quotes',
        href: '/admin/loader-quotes',
        note: 'Managed loader microcopy and reusable quote library',
    },
    {
        label: 'Inbox',
        href: '/admin/contact-submissions',
        note: 'Messages stored from the public contact form',
    },
    {
        label: 'Settings',
        href: '/admin/settings',
        note: 'Bounded runtime configuration',
    },
    {
        label: 'Audit log',
        href: '/admin/audit-log',
        note: 'Recent sensitive operator actions',
    },
] as const;

const user = page.props.auth.user as User | null;
const hoverQuote = ref<LoaderQuote | null>(null);
const currentSection = computed(
    () =>
        navigation.find((item) => page.url.startsWith(item.href)) ??
        navigation[0],
);
const loaderQuotes = computed(() => page.props.site.runtime.loaderQuotes ?? []);

function pickHoverQuote() {
    if (loaderQuotes.value.length === 0) {
        hoverQuote.value = null;
        return;
    }

    hoverQuote.value =
        loaderQuotes.value[Math.floor(Math.random() * loaderQuotes.value.length)];
}
</script>

<template>
    <div class="admin-shell">
        <header class="admin-shell__header">
            <div class="admin-shell__bar">
                <div
                    class="admin-shell__brand"
                    @mouseenter="pickHoverQuote"
                    @mouseleave="hoverQuote = null"
                >
                    <BrandMark :branding="page.props.site.branding" />
                    <p class="type-eyebrow">Admin shell</p>
                    <div v-if="hoverQuote" class="admin-shell__quote">
                        <QuoteLinePreview
                            compact
                            :text="hoverQuote.text"
                            :type="hoverQuote.type"
                            :author="hoverQuote.author"
                        />
                    </div>
                </div>

                <div class="admin-shell__meta">
                    <span class="type-meta admin-shell__user">
                        {{ user?.email }}
                    </span>
                    <Button href="/" variant="secondary" size="sm">
                        View public site
                    </Button>
                    <Link
                        as="button"
                        href="/admin/logout"
                        method="post"
                        class="admin-shell__logout"
                    >
                        Sign out
                    </Link>
                </div>
            </div>
        </header>

        <div class="admin-shell__body">
            <aside class="admin-shell__nav">
                <p class="type-eyebrow">Navigation</p>
                <h2 class="type-h3 admin-shell__nav-title">
                    {{ currentSection.label }}
                </h2>
                <p class="type-body-sm admin-shell__nav-copy">
                    {{ currentSection.note }}
                    <AdminHint
                        label="?"
                        text="Editorial bodies stay markdown-friendly. Runtime presentation stays database-backed. Rebuild from admin when public output changed."
                    />
                </p>

                <nav class="admin-shell__nav-links" aria-label="Admin">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="admin-shell__nav-link"
                        :class="{
                            'admin-shell__nav-link--active':
                                page.url.startsWith(item.href),
                        }"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <Panel class="admin-shell__note" tone="grid">
                    <p class="type-eyebrow">Boundaries</p>
                    <p class="type-body-sm admin-shell__note-copy">
                        Use admin for content operations and runtime presentation.
                        Keep infrastructure secrets in <code>.env</code>.
                    </p>
                </Panel>
            </aside>

            <main class="admin-shell__main">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.admin-shell {
    min-height: 100vh;
    background:
        radial-gradient(
            circle at top left,
            color-mix(in srgb, var(--sw-accent-green) 10%, transparent),
            transparent 34%
        ),
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-grid) 84%, white 16%),
            var(--sw-bg-base)
        );
    color: var(--sw-text-primary);
}

.admin-shell__header {
    position: sticky;
    top: 0;
    z-index: var(--sw-z-header);
    border-bottom: 1px solid
        color-mix(in srgb, var(--sw-border) 82%, transparent);
    background: color-mix(in srgb, var(--sw-bg-base) 88%, transparent);
    backdrop-filter: blur(var(--sw-runtime-surface-blur, 18px));
}

.admin-shell__bar,
.admin-shell__body {
    width: min(
        calc(100% - var(--sw-layout-gutter-lg)),
        var(--sw-admin-shell-max-width)
    );
    margin-inline: auto;
}

.admin-shell__bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--sw-space-sm);
    padding-block: 18px;
}

.admin-shell__brand,
.admin-shell__meta {
    display: flex;
    align-items: center;
    gap: var(--sw-space-xs);
}

.admin-shell__brand {
    position: relative;
    flex-wrap: wrap;
    align-items: baseline;
}

.admin-shell__title {
    margin: 0;
}

.admin-shell__subtitle {
    color: var(--sw-text-secondary);
}

.admin-shell__quote {
    position: absolute;
    inset: calc(100% + 0.65rem) auto auto 0;
    width: min(20rem, 60vw);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 96%, transparent);
    padding: 0.75rem 0.85rem;
    box-shadow: var(--sw-shadow-md);
}

.admin-shell__meta {
    flex-wrap: wrap;
    justify-content: flex-end;
}

.admin-shell__user {
    color: var(--sw-text-secondary);
}

.admin-shell__logout {
    display: inline-flex;
    min-height: 2.5rem;
    align-items: center;
    justify-content: center;
    border-radius: var(--sw-radius-md);
    border: 1px solid var(--sw-border);
    padding-inline: 0.95rem;
    font-size: 13px;
    font-weight: 500;
    transition:
        color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.admin-shell__logout:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .admin-shell__logout:hover {
        border-color: var(--sw-accent-dominant);
        color: var(--sw-accent-dominant);
        transform: translateY(-1px);
    }
}

.admin-shell__body {
    display: grid;
    grid-template-columns: minmax(0, var(--sw-admin-nav-width)) minmax(0, 1fr);
    gap: clamp(var(--sw-space-md), 4vw, var(--sw-space-lg));
    padding-block: clamp(var(--sw-space-md), 4vw, var(--sw-space-xl));
}

.admin-shell__nav {
    position: sticky;
    top: var(--sw-header-offset);
    display: grid;
    gap: var(--sw-space-xs);
    align-self: start;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    padding: var(--sw-space-sm);
    box-shadow: var(--sw-shadow-sm);
}

.admin-shell__nav-title {
    margin: 0;
}

.admin-shell__nav-copy {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    color: var(--sw-text-secondary);
}

.admin-shell__nav-links {
    display: grid;
    gap: 0.35rem;
}

.admin-shell__nav-link {
    border-radius: var(--sw-radius-md);
    padding: 0.8rem 0.9rem;
    font-size: 0.95rem;
    color: var(--sw-text-secondary);
    transition:
        transform var(--sw-motion-fast),
        color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.admin-shell__nav-link--active {
    background: color-mix(in srgb, var(--sw-accent-sun) 14%, transparent);
    color: var(--sw-text-primary);
}

@media (hover: hover) {
    .admin-shell__nav-link:hover {
        color: var(--sw-text-primary);
        transform: translateX(2px);
    }
}

.admin-shell__note {
    display: grid;
    gap: 0.45rem;
    margin-top: 0.35rem;
    padding: var(--sw-space-sm);
}

.admin-shell__note-copy,
.admin-shell__note-meta {
    color: var(--sw-text-secondary);
}

.admin-shell__main {
    min-width: 0;
}

@media (prefers-reduced-motion: reduce) {
    .admin-shell__logout,
    .admin-shell__nav-link {
        transition: none;
    }
}

@media (max-width: 900px) {
    .admin-shell__body {
        grid-template-columns: minmax(0, 1fr);
    }

    .admin-shell__nav {
        position: static;
    }
}

@media (max-width: 640px) {
    .admin-shell__bar,
    .admin-shell__body {
        width: min(
            calc(100% - var(--sw-layout-gutter-sm)),
            var(--sw-admin-shell-max-width)
        );
    }

    .admin-shell__bar {
        align-items: flex-start;
        flex-direction: column;
    }

    .admin-shell__meta {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>
