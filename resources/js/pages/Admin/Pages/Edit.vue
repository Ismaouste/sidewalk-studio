<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminStructuredValueEditor from '@/components/admin/shared/AdminStructuredValueEditor.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { AdminPageEntry, FlashProps, JsonObject } from '@/types';

/**
 * The flat metadata fields only. `payload` is arbitrary nested JSON and is
 * held outside the form: Inertia derives dotted key paths from the value type
 * to address per-field errors, and that derivation cannot terminate on a
 * recursive type. It is merged back in at submit time.
 */
type PageEditForm = Pick<
    AdminPageEntry,
    | 'title'
    | 'description'
    | 'seo_title'
    | 'seo_description'
    | 'robots'
    | 'canonical_url'
    | 'open_graph_image'
>;

const props = defineProps<{ page: AdminPageEntry }>();
const pageState = usePage<{ flash: FlashProps }>();
const status = computed(() => pageState.props.flash?.status ?? null);

const form = useForm<PageEditForm>({
    title: props.page.title,
    description: props.page.description,
    seo_title: props.page.seo_title,
    seo_description: props.page.seo_description,
    robots: props.page.robots,
    canonical_url: props.page.canonical_url,
    open_graph_image: props.page.open_graph_image,
});

const payload = ref<JsonObject>(structuredClone(props.page.payload));

function submit() {
    form.transform((data) => ({ ...data, payload: payload.value })).put(
        `/admin/pages/${props.page.page_key}/${props.page.locale}`,
    );
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Edit ${page.page_key}`" />

        <form class="admin-page-edit" @submit.prevent="submit">
            <header class="admin-page-edit__header">
                <div>
                    <p class="type-eyebrow">Page editor</p>
                    <h1 class="type-h1 admin-page-edit__title">
                        {{ page.page_key }} · {{ page.locale.toUpperCase() }}
                    </h1>
                    <p class="type-body admin-page-edit__copy">
                        Structured edits update runtime SEO fields and page
                        payload blocks without touching rendering code.
                    </p>
                </div>
                <div class="admin-page-edit__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <Button type="submit" :disabled="form.processing"
                        >Save page</Button
                    >
                </div>
            </header>

            <div class="admin-page-edit__grid">
                <Panel class="admin-page-edit__panel" tone="elevated">
                    <label class="admin-page-edit__field">
                        <span class="type-nav">Title</span>
                        <input
                            v-model="form.title"
                            class="admin-page-edit__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">Description</span>
                        <textarea
                            v-model="form.description"
                            class="admin-page-edit__input admin-page-edit__input--textarea"
                            rows="4"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">SEO title</span>
                        <input
                            v-model="form.seo_title"
                            class="admin-page-edit__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">SEO description</span>
                        <textarea
                            v-model="form.seo_description"
                            class="admin-page-edit__input admin-page-edit__input--textarea"
                            rows="4"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">Robots</span>
                        <input
                            v-model="form.robots"
                            class="admin-page-edit__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">Canonical URL</span>
                        <input
                            v-model="form.canonical_url"
                            class="admin-page-edit__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-page-edit__field">
                        <span class="type-nav">Open Graph image</span>
                        <input
                            v-model="form.open_graph_image"
                            class="admin-page-edit__input"
                            type="text"
                        />
                    </label>
                </Panel>

                <Panel class="admin-page-edit__panel" tone="surface">
                    <p class="type-eyebrow">Structured payload</p>
                    <AdminStructuredValueEditor
                        label="Payload"
                        :value="payload"
                        @update:value="payload = $event as JsonObject"
                    />
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-page-edit,
.admin-page-edit__grid,
.admin-page-edit__panel {
    display: grid;
    gap: 1rem;
}

.admin-page-edit__header,
.admin-page-edit__actions {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.admin-page-edit__grid {
    grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
}

.admin-page-edit__panel {
    padding: 1rem;
}

.admin-page-edit__field {
    display: grid;
    gap: 0.45rem;
}

.admin-page-edit__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: 0.85rem 0.95rem;
}

.admin-page-edit__input--textarea {
    min-height: 7rem;
    resize: vertical;
}

@media (max-width: 960px) {
    .admin-page-edit__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 720px) {
    .admin-page-edit__header {
        flex-direction: column;
    }
}
</style>
