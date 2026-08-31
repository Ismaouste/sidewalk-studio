<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminStructuredValueEditor from '@/components/admin/shared/AdminStructuredValueEditor.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, ManagedLanguageFile } from '@/types';

const props = defineProps<{ files: ManagedLanguageFile[] }>();
const activeKey = ref(props.files[0]?.key ?? '');
const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);

const forms = Object.fromEntries(
    props.files.map((file) => [
        file.key,
        useForm<any>({
            payload: structuredClone(file.payload),
        }),
    ]),
) as Record<string, ReturnType<typeof useForm<any>>>;

const activeFile = computed(
    () =>
        props.files.find((file) => file.key === activeKey.value) ??
        props.files[0],
);

function save(key: string) {
    forms[key].put(`/admin/language-files/${key}`);
}
</script>

<template>
    <AdminLayout>
        <Head title="Admin Language Files" />

        <div class="admin-language">
            <header class="admin-language__header">
                <div>
                    <p class="type-eyebrow">Managed copy files</p>
                    <h1 class="type-h1 admin-language__title">
                        Edit visible site strings without touching PHP syntax.
                    </h1>
                </div>
                <span v-if="status" class="type-meta">{{ status }}</span>
            </header>

            <div class="admin-language__grid" v-if="activeFile">
                <Panel class="admin-language__nav" tone="grid">
                    <button
                        v-for="file in props.files"
                        :key="file.key"
                        class="admin-language__tab"
                        :class="{
                            'admin-language__tab--active':
                                file.key === activeKey,
                        }"
                        type="button"
                        @click="activeKey = file.key"
                    >
                        {{ file.label }}
                    </button>
                </Panel>

                <form
                    class="admin-language__editor"
                    @submit.prevent="save(activeFile.key)"
                >
                    <Panel class="admin-language__panel" tone="surface">
                        <div class="admin-language__panel-head">
                            <div>
                                <p class="type-nav">{{ activeFile.label }}</p>
                                <p class="type-meta">{{ activeFile.path }}</p>
                            </div>
                            <Button
                                type="submit"
                                :disabled="forms[activeFile.key].processing"
                                >Save file</Button
                            >
                        </div>
                        <AdminStructuredValueEditor
                            label="Language payload"
                            :value="forms[activeFile.key].payload"
                            @update:value="
                                forms[activeFile.key].payload =
                                    $event as Record<string, unknown>
                            "
                        />
                    </Panel>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.admin-language,
.admin-language__grid,
.admin-language__editor {
    display: grid;
    gap: 1rem;
}

.admin-language__header,
.admin-language__panel-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.admin-language__grid {
    grid-template-columns: 260px minmax(0, 1fr);
}

.admin-language__nav,
.admin-language__panel {
    display: grid;
    gap: 0.8rem;
    padding: 1rem;
}

.admin-language__tab {
    text-align: left;
    border-radius: var(--sw-radius-md);
    padding: 0.8rem 0.9rem;
}

.admin-language__tab--active {
    background: color-mix(in srgb, var(--sw-accent-sun) 14%, transparent);
}

@media (max-width: 900px) {
    .admin-language__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
