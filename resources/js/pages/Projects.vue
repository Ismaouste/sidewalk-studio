<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    caseStudies: ContentItem[];
    tracks: { title: string; summary: string }[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
            <p class="eyebrow">Project map</p>
            <h2
                class="mt-4 text-4xl leading-tight font-semibold text-balance sm:text-5xl"
            >
                Three tracks define the v0.
            </h2>
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <article
                    v-for="track in props.tracks"
                    :key="track.title"
                    class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5"
                >
                    <h3 class="text-lg font-semibold">{{ track.title }}</h3>
                    <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                        {{ track.summary }}
                    </p>
                </article>
            </div>
        </section>

        <section class="mt-8 grid gap-4 md:grid-cols-2">
            <Link
                v-for="item in props.caseStudies"
                :key="item.slug"
                :href="item.url"
                class="panel rounded-[2rem] px-6 py-6 transition hover:-translate-y-0.5"
            >
                <p class="eyebrow">{{ item.client || 'Internal' }}</p>
                <h3 class="mt-3 text-2xl font-semibold">{{ item.title }}</h3>
                <p class="mt-4 text-sm leading-7 text-[var(--muted)]">
                    {{ item.summary }}
                </p>
                <div
                    class="mt-5 flex flex-wrap gap-2 text-xs tracking-[0.18em] text-[var(--muted)] uppercase"
                >
                    <span
                        v-for="tag in item.tags"
                        :key="tag"
                        class="rounded-full border border-[var(--border)] px-3 py-1"
                    >
                        {{ tag }}
                    </span>
                </div>
            </Link>
        </section>
    </SiteLayout>
</template>
