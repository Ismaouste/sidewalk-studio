<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { AdminPageEntry, FlashProps } from '@/types';

const props = defineProps<{ pages: AdminPageEntry[] }>();
const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);
</script>

<template>
    <AdminLayout>
        <Head title="Admin Pages" />

        <div class="admin-pages">
            <header class="admin-pages__header">
                <div>
                    <p class="type-eyebrow">Static pages</p>
                    <h1 class="type-h1 admin-pages__title">
                        Manage public page payloads and SEO.
                    </h1>
                    <p class="type-body admin-pages__copy">
                        Pages remain compatible with file-backed defaults while
                        allowing clean admin overrides.
                    </p>
                </div>
                <span v-if="status" class="type-meta">{{ status }}</span>
            </header>

            <div class="admin-pages__grid">
                <Panel
                    v-for="entry in props.pages"
                    :key="`${entry.page_key}-${entry.locale}`"
                    class="admin-pages__card"
                    tone="surface"
                >
                    <div class="admin-pages__card-head">
                        <div>
                            <p class="type-nav">{{ entry.page_key }}</p>
                            <p class="type-meta admin-pages__meta">
                                {{ entry.locale.toUpperCase() }} ·
                                {{ entry.source_driver }}
                            </p>
                        </div>
                        <Link
                            :href="`/admin/pages/${entry.page_key}/${entry.locale}`"
                            class="admin-pages__link"
                            >Edit</Link
                        >
                    </div>
                    <p class="type-body-sm admin-pages__copy">
                        {{ entry.seo_description }}
                    </p>
                </Panel>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-pages,
.admin-pages__grid {
    display: grid;
    gap: 1rem;
}

.admin-pages__header,
.admin-pages__card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.admin-pages__title {
    margin: 0;
}

.admin-pages__copy,
.admin-pages__meta {
    color: var(--sw-text-secondary);
}

.admin-pages__grid {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

.admin-pages__card {
    display: grid;
    gap: 0.9rem;
    padding: 1rem;
}

.admin-pages__link {
    color: var(--sw-accent-dominant);
}

@media (max-width: 720px) {
    .admin-pages__header {
        flex-direction: column;
    }
}
</style>
