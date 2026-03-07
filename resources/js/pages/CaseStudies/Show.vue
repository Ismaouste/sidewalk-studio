<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import RichText from '@/components/RichText.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { ContentItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    item: ContentItem;
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(18rem,0.4fr)]"
        >
            <article class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
                <Link href="/case-studies" class="eyebrow"
                    >Back to case studies</Link
                >
                <h1
                    class="mt-4 text-4xl leading-tight font-semibold text-balance sm:text-5xl"
                >
                    {{ props.item.title }}
                </h1>
                <p class="mt-5 text-lg leading-8 text-[var(--muted)]">
                    {{ props.item.summary }}
                </p>
                <div class="mt-8">
                    <RichText :html="props.item.body_html" />
                </div>
            </article>

            <aside class="panel rounded-[2.5rem] px-6 py-8">
                <p class="eyebrow">Project frame</p>
                <div class="mt-5 grid gap-4 text-sm text-[var(--muted)]">
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <p
                            class="text-xs tracking-[0.18em] text-[var(--accent-red)] uppercase"
                        >
                            Client
                        </p>
                        <p class="mt-2 text-base text-[var(--ink)]">
                            {{ props.item.client }}
                        </p>
                    </div>
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <p
                            class="text-xs tracking-[0.18em] text-[var(--accent-red)] uppercase"
                        >
                            Role
                        </p>
                        <p class="mt-2 leading-7">{{ props.item.role }}</p>
                    </div>
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <p
                            class="text-xs tracking-[0.18em] text-[var(--accent-red)] uppercase"
                        >
                            Stack
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span
                                v-for="tech in props.item.stack"
                                :key="tech"
                                class="rounded-full border border-[var(--border)] px-3 py-1 text-xs tracking-[0.16em] uppercase"
                            >
                                {{ tech }}
                            </span>
                        </div>
                    </div>
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <p
                            class="text-xs tracking-[0.18em] text-[var(--accent-red)] uppercase"
                        >
                            Outcomes
                        </p>
                        <ul class="mt-3 grid gap-2 leading-7">
                            <li
                                v-for="outcome in props.item.outcomes"
                                :key="outcome"
                            >
                                {{ outcome }}
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </section>
    </SiteLayout>
</template>
