<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import type { SeoPayload } from '@/types';

const props = defineProps<{
    seo: SeoPayload;
}>();

const jsonLdPayloads = computed(() =>
    props.seo.jsonLd.map((schema) => JSON.stringify(schema)),
);
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
        <meta
            v-if="props.seo.openGraph.image"
            property="og:image"
            :content="props.seo.openGraph.image"
        />
        <meta
            v-if="props.seo.openGraph.image_alt"
            property="og:image:alt"
            :content="props.seo.openGraph.image_alt"
        />

        <meta name="twitter:card" :content="props.seo.twitter.card" />
        <meta name="twitter:title" :content="props.seo.twitter.title" />
        <meta
            name="twitter:description"
            :content="props.seo.twitter.description"
        />
        <meta
            v-if="props.seo.twitter.image"
            name="twitter:image"
            :content="props.seo.twitter.image"
        />

        <component
            :is="'script'"
            v-for="(schema, index) in jsonLdPayloads"
            :key="index"
            :head-key="`jsonld-${index}`"
            type="application/ld+json"
            v-text="schema"
        />
    </Head>
</template>
