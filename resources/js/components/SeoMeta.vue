<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
}>();
</script>

<template>
    <Head>
        <title>{{ props.seo.title }}</title>
        <meta name="description" :content="props.seo.description" />
        <meta name="robots" :content="props.seo.robots" />
        <link rel="canonical" :href="props.seo.canonical" />

        <meta property="og:title" :content="props.seo.openGraph.title" />
        <meta
            property="og:description"
            :content="props.seo.openGraph.description"
        />
        <meta property="og:type" :content="props.seo.openGraph.type" />
        <meta property="og:url" :content="props.seo.openGraph.url" />
        <meta
            property="og:site_name"
            :content="props.seo.openGraph.site_name"
        />
        <meta property="og:locale" :content="props.seo.openGraph.locale" />

        <meta name="twitter:card" :content="props.seo.twitter.card" />
        <meta name="twitter:title" :content="props.seo.twitter.title" />
        <meta
            name="twitter:description"
            :content="props.seo.twitter.description"
        />

        <template v-for="(schema, index) in props.seo.jsonLd" :key="index">
            <script type="application/ld+json">
                {{ JSON.stringify(schema) }}
            </script>
        </template>
    </Head>
</template>
