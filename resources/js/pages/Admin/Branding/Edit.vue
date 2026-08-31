<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import BrandMark from '@/components/branding/BrandMark.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { BrandingSettings, FlashProps } from '@/types';

const props = defineProps<{
    brandingSettings: BrandingSettings;
    fallbackVariants: Array<{
        value: BrandingSettings['fallback_variant'];
        label: string;
    }>;
}>();

const page = usePage<{ flash: FlashProps }>();
const status = computed(() => page.props.flash?.status ?? null);
const form = useForm<{
    branding_settings: BrandingSettings;
    branding_upload: File | null;
}>({
    branding_settings: { ...props.brandingSettings },
    branding_upload: null,
});

function submit() {
    form.post('/admin/branding');
}

function setUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    form.branding_upload = target.files?.[0] ?? null;
}
</script>

<template>
    <AdminLayout>
        <Head title="Admin Branding" />

        <form class="admin-branding" @submit.prevent="submit">
            <header class="admin-branding__header">
                <div>
                    <p class="type-eyebrow">Branding</p>
                    <h1 class="type-h1">Shared public/admin identity asset.</h1>
                    <p class="type-body admin-branding__copy">
                        The same active mark is used in the public header and
                        the admin shell.
                    </p>
                </div>
                <div class="admin-branding__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <Button type="submit" :disabled="form.processing"
                        >Save branding</Button
                    >
                </div>
            </header>

            <div class="admin-branding__grid">
                <Panel class="admin-branding__panel" tone="elevated">
                    <p class="type-eyebrow">Active asset</p>
                    <div class="admin-branding__preview">
                        <BrandMark :branding="form.branding_settings" />
                    </div>
                    <label class="admin-branding__field">
                        <span class="type-nav">Asset mode</span>
                        <select
                            v-model="form.branding_settings.asset_mode"
                            class="admin-branding__input"
                        >
                            <option value="uploaded">Uploaded asset</option>
                            <option value="fallback">Generated fallback</option>
                        </select>
                    </label>
                    <label class="admin-branding__field">
                        <span class="type-nav">Upload or replace asset</span>
                        <input
                            class="admin-branding__input"
                            type="file"
                            accept="image/*"
                            @change="setUpload"
                        />
                    </label>
                    <label class="admin-branding__field">
                        <span class="type-nav">Active alt text</span>
                        <input
                            v-model="form.branding_settings.active_alt"
                            class="admin-branding__input"
                            type="text"
                        />
                    </label>
                </Panel>

                <Panel class="admin-branding__panel" tone="surface">
                    <p class="type-eyebrow">Fallback system</p>
                    <label class="admin-branding__field">
                        <span class="type-nav">Fallback variant</span>
                        <select
                            v-model="form.branding_settings.fallback_variant"
                            class="admin-branding__input"
                        >
                            <option
                                v-for="variant in fallbackVariants"
                                :key="variant.value"
                                :value="variant.value"
                            >
                                {{ variant.label }}
                            </option>
                        </select>
                    </label>
                    <label class="admin-branding__field">
                        <span class="type-nav">Fallback label</span>
                        <input
                            v-model="form.branding_settings.fallback_label"
                            class="admin-branding__input"
                            type="text"
                        />
                    </label>
                    <label class="admin-branding__field">
                        <span class="type-nav">Fallback subtitle</span>
                        <input
                            v-model="form.branding_settings.fallback_subtitle"
                            class="admin-branding__input"
                            type="text"
                        />
                    </label>
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-branding,
.admin-branding__grid,
.admin-branding__panel {
    display: grid;
    gap: 1rem;
}

.admin-branding__header,
.admin-branding__actions {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.admin-branding__grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.admin-branding__panel {
    padding: 1rem;
}

.admin-branding__field {
    display: grid;
    gap: 0.45rem;
}

.admin-branding__preview {
    display: grid;
    place-items: start;
    padding: 1rem 0;
}

.admin-branding__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: 0.85rem 0.95rem;
}

@media (max-width: 900px) {
    .admin-branding__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 720px) {
    .admin-branding__header {
        flex-direction: column;
    }
}
</style>
