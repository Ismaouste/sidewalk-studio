<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import SchemaField from '@/components/admin/schema/SchemaField.vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { ContentField, JsonValue } from '@/types';

type EntryForm = Record<string, JsonValue>;

const props = defineProps<{
    locale: string;
    schema: { key: string; label: string; fields: ContentField[] };
    recordFields: string[];
    entry: (EntryForm & { id: number }) | null;
    sibling: { id: number; locale: string; organisation: string } | null;
}>();

const pageState = usePage<{ errors: Record<string, string | string[]> }>();

/**
 * One flat object rather than Inertia's `useForm`.
 *
 * `useForm` keys its error map by the fields it was constructed with, and it
 * derives dotted paths from the value type to address per-field errors — a
 * derivation that cannot terminate on `detail_groups`, which is a list of
 * groups holding lists. The page editor hit exactly this and had to hold its
 * payload outside the form; this screen is entirely that shape, so it holds
 * everything outside and posts it whole.
 */
const values = ref<EntryForm>(blankOrExisting());
const saving = ref(false);

const isNew = computed(() => props.entry === null);

/** The two halves of the form, split on the declaration, not on a guess. */
const recordSchemaFields = computed(() =>
    props.schema.fields.filter((field) =>
        props.recordFields.includes(field.name),
    ),
);

const contentSchemaFields = computed(() =>
    props.schema.fields.filter(
        (field) => !props.recordFields.includes(field.name),
    ),
);

/**
 * Every refusal, not the first one.
 *
 * Read from the shared page props rather than from a form helper, and treated
 * as a list because the declaration answers with a list — one sentence per
 * thing that is wrong.
 */
const errors = computed<string[]>(() => {
    const bag = pageState.props.errors ?? {};

    return Object.values(bag).flatMap((value) =>
        Array.isArray(value) ? value : [value],
    );
});

/**
 * The dates and the label, said once in the form itself.
 *
 * An operator filling in a start date while a label is still set would see
 * nothing change on the public page, and would have no way to know why. So
 * the form says which of the two is currently winning.
 */
const dateNotice = computed<string | null>(() => {
    const label = String(values.value.date_label ?? '').trim();
    const started = String(values.value.started_on ?? '').trim();
    const ended = String(values.value.ended_on ?? '').trim();

    if (label !== '') {
        return `The page will show “${label}”. Clear the label to show the dates instead.`;
    }

    if (started === '') {
        return 'With no start date this position closes the chronology, behind everything dated.';
    }

    return ended === ''
        ? 'With no end date this position reads as current, and stays current without another edit.'
        : null;
});

function blankOrExisting(): EntryForm {
    if (props.entry) {
        return { ...props.entry };
    }

    return {
        locale: props.locale,
        kind: 'professional',
        organisation: '',
        role: '',
        started_on: '',
        ended_on: '',
        date_label: '',
        summary: '',
        paragraphs: [],
        detail_groups: [],
    };
}

function submit(): void {
    saving.value = true;

    const payload = { ...values.value, locale: props.locale };
    const done = { onFinish: () => (saving.value = false) };

    if (isNew.value) {
        router.post('/admin/experience', payload, done);

        return;
    }

    router.put(`/admin/experience/${props.entry?.id}`, payload, done);
}
</script>

<template>
    <AdminLayout>
        <AdminHead
            :title="isNew ? 'New experience entry' : 'Edit experience entry'"
        />

        <form class="admin-experience-edit" @submit.prevent="submit">
            <header class="admin-experience-edit__header">
                <div>
                    <p class="type-eyebrow">Experience record</p>
                    <h1 class="type-h1 admin-experience-edit__title">
                        {{
                            isNew
                                ? 'A new position'
                                : String(values.organisation || 'Untitled')
                        }}
                        · {{ props.locale.toUpperCase() }}
                    </h1>
                    <p class="type-body-sm admin-experience-edit__copy">
                        <template v-if="isNew"
                            >Saving files this in both languages at once, so the
                            chronology never holds a position in one language
                            only. Translating the other is an edit.</template
                        >
                        <template v-else-if="props.sibling"
                            >Its {{ props.sibling.locale.toUpperCase() }} twin
                            is
                            <Link
                                :href="`/admin/experience/${props.sibling.id}`"
                                >{{ props.sibling.organisation }}</Link
                            >.</template
                        >
                    </p>
                </div>

                <div class="admin-experience-edit__actions">
                    <Link
                        href="/admin/experience"
                        class="admin-experience-edit__ghost"
                        >Back</Link
                    >
                    <button
                        type="submit"
                        class="admin-experience-edit__save"
                        :disabled="saving"
                    >
                        {{ isNew ? 'Add position' : 'Save position' }}
                    </button>
                </div>
            </header>

            <p
                v-if="errors.length > 0"
                class="admin-experience-edit__errors"
                role="alert"
            >
                <strong class="type-nav">This position was not saved.</strong>
                <span
                    v-for="error in errors"
                    :key="error"
                    class="type-body-sm"
                    >{{ error }}</span
                >
            </p>

            <div class="admin-experience-edit__grid">
                <Panel class="admin-experience-edit__panel" tone="elevated">
                    <p class="type-eyebrow">The position</p>
                    <SchemaField
                        v-for="field in recordSchemaFields"
                        :key="field.name"
                        :field="field"
                        :value="values[field.name] ?? ''"
                        :path="field.name"
                        @update:value="
                            values = { ...values, [field.name]: $event }
                        "
                    />
                    <p
                        v-if="dateNotice"
                        class="type-body-sm admin-experience-edit__notice"
                    >
                        {{ dateNotice }}
                    </p>
                </Panel>

                <Panel class="admin-experience-edit__panel" tone="surface">
                    <p class="type-eyebrow">What it was</p>
                    <SchemaField
                        v-for="field in contentSchemaFields"
                        :key="field.name"
                        :field="field"
                        :value="values[field.name] ?? ''"
                        :path="field.name"
                        @update:value="
                            values = { ...values, [field.name]: $event }
                        "
                    />
                </Panel>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.admin-experience-edit,
.admin-experience-edit__grid,
.admin-experience-edit__panel {
    display: grid;
    gap: var(--sw-space-2xs);
}

.admin-experience-edit__header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--sw-space-xs);
}

.admin-experience-edit__title {
    margin: var(--sw-space-4xs) 0 0;
}

.admin-experience-edit__copy,
.admin-experience-edit__notice {
    color: var(--sw-text-secondary);
    max-inline-size: 62ch;
    margin: var(--sw-space-4xs) 0 0;
}

.admin-experience-edit__notice {
    border-inline-start: 2px solid var(--sw-accent-dominant);
    padding-inline-start: var(--sw-space-3xs);
}

.admin-experience-edit__actions {
    display: flex;
    gap: var(--sw-space-3xs);
}

.admin-experience-edit__ghost,
.admin-experience-edit__save {
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-sm);
    padding: var(--sw-space-4xs) var(--sw-space-3xs);
    background: transparent;
    color: var(--sw-text-primary);
    font: inherit;
    text-decoration: none;
    cursor: pointer;
    min-block-size: 44px;
    display: inline-flex;
    align-items: center;
}

.admin-experience-edit__save {
    border-color: var(--sw-button-primary-bg);
    background: var(--sw-button-primary-bg);
    color: var(--sw-button-primary-text);
}

.admin-experience-edit__save:disabled {
    cursor: progress;
    opacity: 0.7;
}

.admin-experience-edit__grid {
    grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
    align-items: start;
}

.admin-experience-edit__panel {
    padding: var(--sw-space-xs);
    min-inline-size: 0;
}

.admin-experience-edit__errors {
    display: grid;
    gap: var(--sw-space-4xs);
    border: 1px solid var(--sw-accent-dominant);
    border-radius: var(--sw-radius-sm);
    padding: var(--sw-space-3xs);
}

@media (max-width: 900px) {
    .admin-experience-edit__grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .admin-experience-edit__header,
    .admin-experience-edit__actions {
        flex-direction: column;
    }
}
</style>
