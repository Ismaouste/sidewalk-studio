<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import QuoteLinePreview from '@/components/shared/QuoteLinePreview.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, PublicationAdminEntry } from '@/types';

const props = defineProps<{
    mode: 'create' | 'edit';
    publication: PublicationAdminEntry & {
        original_publication_type?: 'note' | 'journal' | 'case_study';
        original_locale?: 'en' | 'fr';
        original_slug?: string;
    };
}>();

const page = usePage<{ flash: FlashProps }>();
const form = useForm({
    original_publication_type: props.publication.original_publication_type ?? props.publication.publication_type,
    original_locale: props.publication.original_locale ?? props.publication.locale,
    original_slug: props.publication.original_slug ?? props.publication.slug,
    publication_type: props.publication.publication_type,
    locale: props.publication.locale,
    title: props.publication.title,
    slug: props.publication.slug,
    summary: props.publication.summary,
    status: props.publication.status,
    published_at: props.publication.published_at,
    updated_at: props.publication.updated_at,
    tags: [...props.publication.tags],
    seo_title: props.publication.seo_title,
    seo_description: props.publication.seo_description,
    robots: props.publication.robots,
    canonical_url: props.publication.canonical_url,
    featured_image: props.publication.featured_image,
    featured_image_alt: props.publication.featured_image_alt,
    open_graph_image: props.publication.open_graph_image,
    featured_video: props.publication.featured_video,
    category: props.publication.category,
    accent_tone: props.publication.accent_tone,
    source_path: props.publication.source_path,
    body_markdown: props.publication.body_markdown,
    metadata: {
        client: props.publication.metadata?.client ?? '',
        role: props.publication.metadata?.role ?? '',
        stack: [...(props.publication.metadata?.stack ?? [])],
        outcomes: [...(props.publication.metadata?.outcomes ?? [])],
    },
});

const status = computed(() => page.props.flash?.status ?? null);
const submitLabel = computed(() => (form.processing ? 'Saving…' : 'Save publication'));

function submit() {
    const url =
        props.mode === 'create'
            ? '/admin/publications'
            : `/admin/publications/${props.publication.original_publication_type ?? props.publication.publication_type}/${props.publication.original_locale ?? props.publication.locale}/${props.publication.original_slug ?? props.publication.slug}`;

    if (props.mode === 'create') {
        form.post(url);
        return;
    }

    form.put(url);
}

function addTag() {
    form.tags.push('');
}

function addMetadataItem(key: 'stack' | 'outcomes') {
    form.metadata[key].push('');
}
</script>

<template>
    <AdminLayout>
        <Head :title="mode === 'create' ? 'Create Publication' : 'Edit Publication'" />

        <form class="admin-publication" @submit.prevent="submit">
            <header class="admin-publication__header">
                <div>
                    <p class="type-eyebrow">Publication editor</p>
                    <h1 class="type-h1 admin-publication__title">{{ mode === 'create' ? 'Create a publication' : publication.title }}</h1>
                    <p class="type-body admin-publication__copy">
                        File-backed legacy entries become database-managed as soon as they are saved here.
                        Metadata lives in the database. The long-form body stays in the linked markdown file.
                    </p>
                </div>

                <div class="admin-publication__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
                </div>
            </header>

            <div class="admin-publication__grid">
                <Panel class="admin-publication__panel" tone="elevated">
                    <p class="type-eyebrow">Identity</p>
                    <label class="admin-publication__field">
                        <span class="type-nav">Type</span>
                        <select v-model="form.publication_type" class="admin-publication__input">
                            <option value="note">Note</option>
                            <option value="journal">Journal</option>
                            <option value="case_study">Case study</option>
                        </select>
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Locale</span>
                        <select v-model="form.locale" class="admin-publication__input">
                            <option value="en">English</option>
                            <option value="fr">French</option>
                        </select>
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Title</span>
                        <input v-model="form.title" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Slug</span>
                        <input v-model="form.slug" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Markdown source path</span>
                        <input v-model="form.source_path" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Summary</span>
                        <textarea v-model="form.summary" class="admin-publication__input admin-publication__input--textarea" rows="4" />
                    </label>
                </Panel>

                <Panel class="admin-publication__panel" tone="surface">
                    <p class="type-eyebrow">Publishing</p>
                    <label class="admin-publication__field">
                        <span class="type-nav">Status</span>
                        <select v-model="form.status" class="admin-publication__input">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Publish date</span>
                        <input v-model="form.published_at" class="admin-publication__input" type="date" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Updated date</span>
                        <input v-model="form.updated_at" class="admin-publication__input" type="date" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Category</span>
                        <input v-model="form.category" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Accent tone</span>
                        <input v-model="form.accent_tone" class="admin-publication__input" type="text" />
                    </label>
                </Panel>

                <Panel class="admin-publication__panel" tone="grid">
                    <p class="type-eyebrow">SEO and media</p>
                    <label class="admin-publication__field">
                        <span class="type-nav">SEO title</span>
                        <input v-model="form.seo_title" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">SEO description</span>
                        <textarea v-model="form.seo_description" class="admin-publication__input admin-publication__input--textarea" rows="4" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Robots</span>
                        <input v-model="form.robots" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Canonical URL</span>
                        <input v-model="form.canonical_url" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Featured image</span>
                        <input v-model="form.featured_image" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Featured image alt</span>
                        <input v-model="form.featured_image_alt" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Open Graph image</span>
                        <input v-model="form.open_graph_image" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Featured video</span>
                        <input v-model="form.featured_video" class="admin-publication__input" type="text" />
                    </label>
                </Panel>

                <Panel class="admin-publication__panel" tone="surface">
                    <div class="admin-publication__panel-head">
                        <p class="type-eyebrow">Tags</p>
                        <button class="admin-publication__mini" type="button" @click="addTag">Add tag</button>
                    </div>
                    <div v-for="(_, index) in form.tags" :key="`tag-${index}`" class="admin-publication__row">
                        <input v-model="form.tags[index]" class="admin-publication__input" type="text" />
                    </div>
                </Panel>

                <Panel class="admin-publication__panel" tone="grid">
                    <p class="type-eyebrow">Case-study metadata</p>
                    <label class="admin-publication__field">
                        <span class="type-nav">Client</span>
                        <input v-model="form.metadata.client" class="admin-publication__input" type="text" />
                    </label>
                    <label class="admin-publication__field">
                        <span class="type-nav">Role</span>
                        <input v-model="form.metadata.role" class="admin-publication__input" type="text" />
                    </label>
                    <div class="admin-publication__panel-head">
                        <p class="type-nav">Stack</p>
                        <button class="admin-publication__mini" type="button" @click="addMetadataItem('stack')">Add stack item</button>
                    </div>
                    <div v-for="(_, index) in form.metadata.stack" :key="`stack-${index}`" class="admin-publication__row">
                        <input v-model="form.metadata.stack[index]" class="admin-publication__input" type="text" />
                    </div>
                    <div class="admin-publication__panel-head">
                        <p class="type-nav">Outcomes</p>
                        <button class="admin-publication__mini" type="button" @click="addMetadataItem('outcomes')">Add outcome</button>
                    </div>
                    <div v-for="(_, index) in form.metadata.outcomes" :key="`outcome-${index}`" class="admin-publication__row">
                        <input v-model="form.metadata.outcomes[index]" class="admin-publication__input" type="text" />
                    </div>
                </Panel>

                <Panel class="admin-publication__panel admin-publication__panel--full" tone="elevated">
                    <div class="admin-publication__markdown-grid">
                        <label class="admin-publication__field">
                            <span class="type-nav">Markdown body</span>
                            <textarea v-model="form.body_markdown" class="admin-publication__input admin-publication__input--editor" rows="20" />
                        </label>
                        <div class="admin-publication__preview">
                            <p class="type-eyebrow">Preview</p>
                            <QuoteLinePreview
                                :text="form.title || 'Draft title'"
                                type="message"
                                :author="`${form.locale.toUpperCase()} · ${form.publication_type}`"
                                compact
                            />
                            <pre class="admin-publication__preview-body">{{ form.body_markdown }}</pre>
                        </div>
                    </div>
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-publication,
.admin-publication__grid,
.admin-publication__panel {
    display: grid;
    gap: 1rem;
}

.admin-publication__header,
.admin-publication__actions,
.admin-publication__panel-head,
.admin-publication__row {
    display: flex;
    gap: 0.8rem;
}

.admin-publication__header {
    align-items: flex-start;
    justify-content: space-between;
}

.admin-publication__actions {
    align-items: center;
}

.admin-publication__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.admin-publication__panel {
    padding: 1rem;
}

.admin-publication__panel--full {
    grid-column: 1 / -1;
}

.admin-publication__field {
    display: grid;
    gap: 0.45rem;
}

.admin-publication__markdown-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: minmax(0, 1.3fr) minmax(18rem, 0.7fr);
}

.admin-publication__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: 0.85rem 0.95rem;
}

.admin-publication__input--textarea {
    min-height: 7rem;
    resize: vertical;
}

.admin-publication__input--editor {
    min-height: 22rem;
    resize: vertical;
    font-family: var(--sw-font-code);
}

.admin-publication__mini {
    color: var(--sw-accent-dominant);
}

.admin-publication__preview {
    display: grid;
    gap: 0.8rem;
    align-content: start;
}

.admin-publication__preview-body {
    overflow: auto;
    max-block-size: 30rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    padding: 1rem;
    color: var(--sw-text-secondary);
}

@media (max-width: 900px) {
    .admin-publication__grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .admin-publication__markdown-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 720px) {
    .admin-publication__header {
        flex-direction: column;
    }
}
</style>
