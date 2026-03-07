<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import type { NavItem, SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const navigation = computed(() => page.props.site.navigation);
const currentUrl = computed(() => page.url);

function isActive(item: NavItem) {
    if (item.href === '/') {
        return currentUrl.value === '/';
    }

    return currentUrl.value.startsWith(item.href);
}
</script>

<template>
    <div
        class="relative min-h-screen overflow-hidden bg-[var(--background)] text-[var(--ink)]"
    >
        <div class="ambient-grid" />
        <div
            class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8"
        >
            <header
                class="panel sticky top-4 z-20 flex flex-col gap-4 rounded-[2rem] px-5 py-4 md:flex-row md:items-center md:justify-between"
            >
                <Link href="/" class="max-w-xl text-balance">
                    <p
                        class="text-xs tracking-[0.3em] text-[var(--accent-red)] uppercase"
                    >
                        {{ page.props.site.name }}
                    </p>
                    <h1 class="text-lg font-semibold">
                        {{ page.props.site.tagline }}
                    </h1>
                </Link>
                <nav class="flex flex-wrap gap-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-full px-3 py-2 text-sm transition"
                        :class="
                            isActive(item)
                                ? 'bg-[var(--ink)] text-[var(--background)]'
                                : 'text-[var(--muted)] hover:bg-white/70 hover:text-[var(--ink)]'
                        "
                    >
                        {{ item.label }}
                    </Link>
                </nav>
            </header>

            <main class="flex-1 py-10">
                <slot />
            </main>

            <footer
                class="panel mt-8 flex flex-col gap-4 rounded-[2rem] px-5 py-5 md:flex-row md:items-end md:justify-between"
            >
                <div class="max-w-2xl space-y-2">
                    <p
                        class="text-xs tracking-[0.28em] text-[var(--accent-green)] uppercase"
                    >
                        Build notes
                    </p>
                    <p class="text-sm text-[var(--muted)]">
                        Local-first Laravel v0. English-only content.
                        Consent-aware embeds. SSR prepared, not activated.
                    </p>
                </div>
                <div
                    class="flex flex-wrap items-center gap-3 text-sm text-[var(--muted)]"
                >
                    <a
                        class="hover:text-[var(--ink)]"
                        :href="`mailto:${page.props.site.contact.email}`"
                        >{{ page.props.site.contact.email }}</a
                    >
                    <span>{{ page.props.site.contact.location }}</span>
                    <ConsentPreferencesButton />
                </div>
            </footer>
        </div>
    </div>
</template>
