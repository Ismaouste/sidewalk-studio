<script setup lang="ts">
import MediaEmbed from '@/components/MediaEmbed.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { LabItem, SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    labs: LabItem[];
    embedDemo: {
        title: string;
        description: string;
        service: string;
        id: string;
    };
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.9fr)]"
        >
            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
                <p class="eyebrow">Labs</p>
                <h2
                    class="mt-4 text-4xl leading-tight font-semibold text-balance sm:text-5xl"
                >
                    Sandbox the risky parts before they reach production code.
                </h2>
                <div class="mt-8 grid gap-4">
                    <article
                        v-for="lab in props.labs"
                        :key="lab.slug"
                        class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-semibold">
                                {{ lab.title }}
                            </h3>
                            <span
                                class="rounded-full border border-[var(--border)] px-3 py-1 text-xs tracking-[0.18em] text-[var(--muted)] uppercase"
                                >{{ lab.status }}</span
                            >
                        </div>
                        <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                            {{ lab.summary }}
                        </p>
                        <div
                            class="mt-4 flex flex-wrap gap-2 text-xs tracking-[0.16em] text-[var(--muted)] uppercase"
                        >
                            <span
                                v-for="item in lab.stack"
                                :key="item"
                                class="rounded-full border border-[var(--border)] px-3 py-1"
                            >
                                {{ item }}
                            </span>
                        </div>
                    </article>
                </div>
            </div>

            <aside class="panel rounded-[2.5rem] px-6 py-8">
                <p class="eyebrow">Consent demo</p>
                <h3 class="mt-3 text-2xl font-semibold">
                    {{ props.embedDemo.title }}
                </h3>
                <p class="mt-3 text-sm leading-7 text-[var(--muted)]">
                    {{ props.embedDemo.description }}
                </p>
                <div class="mt-6">
                    <MediaEmbed
                        :title="props.embedDemo.title"
                        :service="props.embedDemo.service"
                        :id="props.embedDemo.id"
                    />
                </div>
            </aside>
        </section>
    </SiteLayout>
</template>
