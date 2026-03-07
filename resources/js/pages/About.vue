<script setup lang="ts">
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    principles: string[];
    timeline: string[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section
            class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(18rem,0.85fr)]"
        >
            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
                <p class="eyebrow">About the build</p>
                <h2
                    class="mt-4 max-w-4xl text-4xl leading-tight font-semibold text-balance sm:text-5xl"
                >
                    A portfolio designed as a calm engineering object.
                </h2>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-[var(--muted)]">
                    Sidewalk Studio exists to make technical judgment visible:
                    not only the final UI, but also the quality of the system
                    underneath it.
                </p>
                <div class="mt-8 grid gap-4">
                    <div
                        v-for="principle in props.principles"
                        :key="principle"
                        class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-5 text-base leading-7 text-[var(--muted)]"
                    >
                        {{ principle }}
                    </div>
                </div>
            </div>

            <aside class="panel rounded-[2.5rem] px-6 py-8">
                <p class="eyebrow">Trajectory</p>
                <ol class="mt-5 grid gap-4">
                    <li
                        v-for="step in props.timeline"
                        :key="step"
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4 text-sm leading-7 text-[var(--muted)]"
                    >
                        {{ step }}
                    </li>
                </ol>
            </aside>
        </section>
    </SiteLayout>
</template>
