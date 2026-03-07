<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, LabItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    highlights: string[];
    featuredCaseStudies: ContentItem[];
    latestWriting: ContentItem[];
    labs: LabItem[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section
            class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.9fr)]"
        >
            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
                <p class="eyebrow">{{ props.hero.eyebrow }}</p>
                <h2
                    class="mt-4 max-w-4xl text-4xl leading-tight font-semibold text-balance sm:text-5xl lg:text-6xl"
                >
                    {{ props.hero.title }}
                </h2>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-[var(--muted)]">
                    {{ props.hero.summary }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        href="/case-studies"
                        class="rounded-full bg-[var(--ink)] px-5 py-3 text-sm font-semibold text-[var(--background)] transition hover:bg-[var(--accent-green)]"
                    >
                        Explore case studies
                    </Link>
                    <Link
                        href="/writing"
                        class="rounded-full border border-[var(--border-strong)] px-5 py-3 text-sm font-semibold text-[var(--ink)] transition hover:bg-white/70"
                    >
                        Read the writing log
                    </Link>
                </div>
            </div>

            <aside class="panel rounded-[2.5rem] px-6 py-8">
                <p class="eyebrow">Current frame</p>
                <ul class="mt-5 grid gap-4">
                    <li
                        v-for="highlight in props.highlights"
                        :key="highlight"
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4 text-sm leading-7 text-[var(--muted)]"
                    >
                        {{ highlight }}
                    </li>
                </ul>
            </aside>
        </section>

        <section class="mt-8 grid gap-6 xl:grid-cols-2">
            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="eyebrow">Case studies</p>
                        <h3 class="mt-3 text-2xl font-semibold">
                            Recent build decisions
                        </h3>
                    </div>
                    <Link
                        href="/case-studies"
                        class="text-sm font-semibold text-[var(--accent-green)]"
                        >View all</Link
                    >
                </div>
                <div class="mt-6 grid gap-4">
                    <Link
                        v-for="item in props.featuredCaseStudies"
                        :key="item.slug"
                        :href="item.url"
                        class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5 transition hover:-translate-y-0.5 hover:border-[var(--accent-green)]"
                    >
                        <p
                            class="text-xs tracking-[0.28em] text-[var(--accent-red)] uppercase"
                        >
                            {{ item.client || 'Internal build' }}
                        </p>
                        <h4
                            class="mt-3 text-xl font-semibold text-[var(--ink)]"
                        >
                            {{ item.title }}
                        </h4>
                        <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                            {{ item.summary }}
                        </p>
                    </Link>
                </div>
            </div>

            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="eyebrow">Writing</p>
                        <h3 class="mt-3 text-2xl font-semibold">
                            Notes from the system build
                        </h3>
                    </div>
                    <Link
                        href="/writing"
                        class="text-sm font-semibold text-[var(--accent-green)]"
                        >Open archive</Link
                    >
                </div>
                <div class="mt-6 grid gap-4">
                    <Link
                        v-for="item in props.latestWriting"
                        :key="item.slug"
                        :href="item.url"
                        class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5 transition hover:-translate-y-0.5 hover:border-[var(--accent-red)]"
                    >
                        <div
                            class="flex items-center justify-between gap-3 text-xs tracking-[0.22em] text-[var(--muted)] uppercase"
                        >
                            <span>{{ item.published_at }}</span>
                            <span>{{ item.reading_time }} min</span>
                        </div>
                        <h4
                            class="mt-3 text-xl font-semibold text-[var(--ink)]"
                        >
                            {{ item.title }}
                        </h4>
                        <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                            {{ item.summary }}
                        </p>
                    </Link>
                </div>
            </div>
        </section>

        <section class="panel mt-8 rounded-[2.5rem] px-6 py-8 sm:px-8">
            <p class="eyebrow">Labs</p>
            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                <article
                    v-for="lab in props.labs"
                    :key="lab.slug"
                    class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5"
                >
                    <div class="flex items-center justify-between gap-3">
                        <h4 class="text-lg font-semibold">{{ lab.title }}</h4>
                        <span
                            class="rounded-full border border-[var(--border)] px-3 py-1 text-xs tracking-[0.18em] text-[var(--muted)] uppercase"
                            >{{ lab.status }}</span
                        >
                    </div>
                    <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                        {{ lab.summary }}
                    </p>
                </article>
            </div>
        </section>
    </SiteLayout>
</template>
