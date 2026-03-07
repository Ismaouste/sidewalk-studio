<script setup lang="ts">
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload, SiteContact } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
    contact: SiteContact;
    services: string[];
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="props.seo" />

        <section
            class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.85fr)]"
        >
            <div class="panel rounded-[2.5rem] px-6 py-8 sm:px-8 sm:py-10">
                <p class="eyebrow">Contact</p>
                <h2
                    class="mt-4 text-4xl leading-tight font-semibold text-balance sm:text-5xl"
                >
                    Privacy, SEO, and Laravel modernization conversations.
                </h2>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-[var(--muted)]">
                    {{ props.contact.availability }}
                </p>
                <ul class="mt-8 grid gap-4">
                    <li
                        v-for="service in props.services"
                        :key="service"
                        class="rounded-[1.8rem] border border-[var(--border)] bg-white/70 px-5 py-4 text-sm leading-7 text-[var(--muted)]"
                    >
                        {{ service }}
                    </li>
                </ul>
            </div>

            <aside class="panel rounded-[2.5rem] px-6 py-8">
                <p class="eyebrow">Details</p>
                <dl class="mt-5 grid gap-4 text-sm text-[var(--muted)]">
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <dt
                            class="text-xs tracking-[0.24em] text-[var(--accent-red)] uppercase"
                        >
                            Email
                        </dt>
                        <dd class="mt-2 text-base text-[var(--ink)]">
                            {{ props.contact.email }}
                        </dd>
                    </div>
                    <div
                        class="rounded-[1.6rem] border border-[var(--border)] bg-white/60 px-4 py-4"
                    >
                        <dt
                            class="text-xs tracking-[0.24em] text-[var(--accent-red)] uppercase"
                        >
                            Location
                        </dt>
                        <dd class="mt-2 text-base text-[var(--ink)]">
                            {{ props.contact.location }}
                        </dd>
                    </div>
                </dl>
            </aside>
        </section>
    </SiteLayout>
</template>
