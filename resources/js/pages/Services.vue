<script setup lang="ts">
import SectionIntro from '@/components/design-system/SectionIntro.vue';
import SeoMeta from '@/components/SeoMeta.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import type { SeoPayload } from '@/types';

export interface ServiceOffer {
    label: string;
    title: string;
    summary: string;
    price: string;
    price_meta?: string;
    points: string[];
    cta: string;
    tone: 'dominant' | 'green' | 'sun' | 'coral' | 'violet';
}

defineProps<{
    seo: SeoPayload;
    hero: {
        eyebrow: string;
        title: string;
        summary: string;
    };
    offers: ServiceOffer[];
    modifiers: {
        title: string;
        summary: string;
    };
    engagement: {
        title: string;
        steps: Array<{ title: string; summary: string }>;
    };
    legalNote: string;
    contactCta: {
        title: string;
        summary: string;
    };
    cvDownloads: Array<{ label: string; href: string }>;
}>();
</script>

<template>
    <SiteLayout>
        <SeoMeta :seo="seo" />

        <section class="sw-section services-page">
            <SectionIntro
                :eyebrow="hero.eyebrow"
                :title="hero.title"
                :description="hero.summary"
            />
        </section>
    </SiteLayout>
</template>
