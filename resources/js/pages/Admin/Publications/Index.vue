<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, PublicationAdminEntry, PublicationTypeSetting } from '@/types';

const props = defineProps<{
    groups: Record<'note' | 'journal' | 'case_study', PublicationAdminEntry[]>;
    typeSettings: PublicationTypeSetting[];
}>();

const page = usePage<{ flash: FlashProps }>();
const settingsForm = useForm({
    settings: props.typeSettings.map((setting) => ({ ...setting })),
});
const status = computed(() => page.props.flash?.status ?? null);

function labelFor(type: 'note' | 'journal' | 'case_study') {
    return {
        note: 'Notes',
        journal: 'Journal entries',
        case_study: 'Case studies',
    }[type];
}

function submitTypeSettings() {
    settingsForm.put('/admin/publication-type-settings', {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="Admin Publications" />

        <div class="admin-collection">
            <header class="admin-collection__header">
                <div>
                    <p class="type-eyebrow">Publications</p>
                    <h1 class="type-h1 admin-collection__title">Manage notes, journal entries, and case studies.</h1>
                    <p class="type-body admin-collection__copy">
                        This screen merges file-backed legacy content with database-backed edits and new entries.
                    </p>
                </div>
                <span v-if="status" class="type-meta admin-collection__status">{{ status }}</span>
            </header>

            <Panel class="admin-collection__settings" tone="elevated">
                <div class="admin-collection__settings-head">
                    <div>
                        <p class="type-eyebrow">Type presentation settings</p>
                        <h2 class="type-h3">CTA and accent controls</h2>
                    </div>
                    <Button type="button" :disabled="settingsForm.processing || !settingsForm.isDirty" @click="submitTypeSettings">
                        Save type settings
                    </Button>
                </div>

                <div class="admin-collection__settings-grid">
                    <Panel
                        v-for="(setting, index) in settingsForm.settings"
                        :key="setting.type"
                        class="admin-collection__setting-card"
                        tone="grid"
                    >
                        <p class="type-nav">{{ labelFor(setting.type) }}</p>
                        <label class="admin-collection__field">
                            <span class="type-meta">CTA label</span>
                            <input v-model="settingsForm.settings[index].cta_label" class="admin-collection__input" type="text" />
                        </label>
                        <label class="admin-collection__field">
                            <span class="type-meta">CTA target</span>
                            <input v-model="settingsForm.settings[index].cta_target" class="admin-collection__input" type="text" />
                        </label>
                        <label class="admin-collection__field">
                            <span class="type-meta">Accent color token</span>
                            <input v-model="settingsForm.settings[index].accent_color" class="admin-collection__input" type="text" />
                        </label>
                    </Panel>
                </div>
            </Panel>

            <section
                v-for="type in ['note', 'journal', 'case_study'] as const"
                :key="type"
                class="admin-collection__section"
            >
                <div class="admin-collection__section-head">
                    <div>
                        <p class="type-eyebrow">{{ labelFor(type) }}</p>
                        <h2 class="type-h2">{{ props.groups[type].length }} entries</h2>
                    </div>
                    <Button :href="`/admin/publications/create/${type}`">Create {{ labelFor(type).slice(0, -1) }}</Button>
                </div>

                <div class="admin-collection__list">
                    <Panel
                        v-for="item in props.groups[type]"
                        :key="`${item.locale}-${item.slug}`"
                        class="admin-collection__card"
                        tone="surface"
                    >
                        <div class="admin-collection__card-head">
                            <div>
                                <p class="type-nav">{{ item.title }}</p>
                                <p class="type-meta admin-collection__meta">
                                    {{ item.locale.toUpperCase() }} · {{ item.status }} · {{ item.source_driver }}
                                </p>
                            </div>
                            <Link :href="`/admin/publications/${item.publication_type}/${item.locale}/${item.slug}`" class="admin-collection__link">
                                Edit
                            </Link>
                        </div>
                        <p class="type-body-sm admin-collection__summary">{{ item.summary }}</p>
                        <p class="type-meta admin-collection__meta">Slug: {{ item.slug }}</p>
                    </Panel>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-collection,
.admin-collection__section,
.admin-collection__list {
    display: grid;
    gap: 1rem;
}

.admin-collection__header,
.admin-collection__section-head,
.admin-collection__settings-head,
.admin-collection__card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.admin-collection__title {
    margin: 0;
}

.admin-collection__copy,
.admin-collection__meta,
.admin-collection__summary {
    color: var(--sw-text-secondary);
}

.admin-collection__settings,
.admin-collection__setting-card,
.admin-collection__card {
    display: grid;
    gap: 0.9rem;
    padding: 1rem;
}

.admin-collection__settings-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

.admin-collection__field {
    display: grid;
    gap: 0.4rem;
}

.admin-collection__input {
    min-height: 2.8rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding-inline: 0.9rem;
}

.admin-collection__link {
    color: var(--sw-accent-dominant);
}

@media (max-width: 720px) {
    .admin-collection__header,
    .admin-collection__section-head,
    .admin-collection__settings-head,
    .admin-collection__card-head {
        flex-direction: column;
    }
}
</style>
