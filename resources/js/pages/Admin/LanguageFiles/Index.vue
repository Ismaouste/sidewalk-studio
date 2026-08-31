<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import AdminStructuredValueEditor from '@/components/admin/shared/AdminStructuredValueEditor.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { FlashProps, JsonObject, ManagedLanguageFile } from '@/types';

const props = defineProps<{ files: ManagedLanguageFile[] }>();
const activeKey = ref(props.files[0]?.key ?? '');
const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);

/**
 * Deliberately plain state rather than useForm. A language file's payload is
 * arbitrary nested JSON, and Inertia's form helper derives dotted key paths
 * from the value type — which cannot terminate on a recursive one. Nothing
 * here needs per-field errors or dirty tracking, only a payload and a pending
 * flag, so the router call is both simpler and typeable.
 */
const payloads = reactive<Record<string, JsonObject>>(
    Object.fromEntries(
        props.files.map((file) => [file.key, structuredClone(file.payload)]),
    ),
);

const savingKey = ref<string | null>(null);

const activeFile = computed(
    () =>
        props.files.find((file) => file.key === activeKey.value) ??
        props.files[0],
);

const activePayload = computed(() =>
    activeFile.value ? payloads[activeFile.value.key] : undefined,
);

function save(key: string) {
    const payload = payloads[key];

    if (!payload || savingKey.value) {
        return;
    }

    savingKey.value = key;

    router.put(
        `/admin/language-files/${key}`,
        { payload },
        {
            preserveScroll: true,
            onFinish: () => {
                savingKey.value = null;
            },
        },
    );
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

            <div v-if="activeFile" class="admin-language__grid">
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
                    v-if="activeFile && activePayload"
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
                                :disabled="savingKey === activeFile.key"
                                >Save file</Button
                            >
                        </div>
                        <AdminStructuredValueEditor
                            label="Language payload"
                            :value="activePayload"
                            @update:value="
                                payloads[activeFile.key] = $event as JsonObject
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
