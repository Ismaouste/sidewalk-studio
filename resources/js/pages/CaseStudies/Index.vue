<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    items: ContentItem[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
            <p class="eyebrow">Case studies</p>
            <h2
                class="mt-4 text-4xl leading-tight font-semibold text-balance sm:text-5xl"
            >
                Detailed decisions behind the first Sidewalk Studio release.
            </h2>
            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <Link
                    v-for="item in props.items"
                    :key="item.slug"
                    :href="item.url"
                    class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5 transition hover:-translate-y-0.5 hover:border-[var(--accent-red)]"
                >
                    <p class="eyebrow">{{ item.client || 'Internal build' }}</p>
                    <h3 class="mt-3 text-2xl font-semibold">
                        {{ item.title }}
                    </h3>
                    <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                        {{ item.summary }}
                    </p>
                    <div
                        class="mt-5 flex flex-wrap gap-2 text-xs tracking-[0.16em] text-[var(--muted)] uppercase"
                    >
                        <span
                            v-for="tech in item.stack"
                            :key="tech"
                            class="rounded-full border border-[var(--border)] px-3 py-1"
                        >
                            {{ tech }}
                        </span>
                    </div>
                </Link>
            </div>
        </section>
    </SiteLayout>
</template>
