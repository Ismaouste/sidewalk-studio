<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SiteProps } from '@/types';

/**
 * The tab label for an admin screen.
 *
 * Public pages have their title composed in `App\Support\Seo`, which knows
 * the locale and reads the name an operator can edit from
 * /admin/language-files. Admin screens have no such server-side composer —
 * they name themselves — so they compose here, from the same shared prop,
 * rather than from the build-time `VITE_APP_NAME` that used to suffix every
 * title in the application and could only ever spell one locale's version of
 * the name.
 *
 * The separator matches the public one, so an operator with both open reads
 * one house style across the tab strip.
 */
const props = defineProps<{ title: string }>();

const page = usePage<{ site: SiteProps }>();

const documentTitle = computed(
    () => `${props.title} · ${page.props.site.name}`,
);
</script>

<template>
    <Head :title="documentTitle" />
</template>
