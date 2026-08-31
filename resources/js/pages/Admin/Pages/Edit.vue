<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import SchemaField from '@/components/admin/schema/SchemaField.vue';
import AdminHead from '@/components/admin/shared/AdminHead.vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type {
    AdminPageEntry,
    ContentField,
    ContentSchema,
    FlashProps,
    JsonObject,
    JsonValue,
} from '@/types';

/**
 * The flat metadata fields only. `payload` is nested and is held outside the
 * form: Inertia derives dotted key paths from the value type to address
 * per-field errors, and that derivation cannot terminate on a recursive type.
 * It is merged back in at submit time.
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

const props = defineProps<{
    page: AdminPageEntry;
    schema: ContentSchema;
    metaFields: string[];
    hasSeed: boolean;
}>();

const pageState = usePage<{
    flash: FlashProps;
    errors: Record<string, string | string[]>;
}>();
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

/**
 * A JSON round-trip rather than `structuredClone`, which is what this line
 * used to be and which threw `DataCloneError` on every load: Inertia hands
 * props through a reactive proxy, and `structuredClone` refuses a proxy. The
 * page editor has been failing to mount since long before this feature — a
 * plausible reason nobody minded is that anything saved from it was ignored
 * by the public site anyway.
 *
 * The payload is JSON by definition, so a round-trip is both correct and
 * immune to whatever the props are wrapped in.
 */
const payload = ref<JsonObject>(clonePayload());

function clonePayload(): JsonObject {
    return JSON.parse(JSON.stringify(props.page.payload)) as JsonObject;
}

/**
 * Reverting redirects back to this same editor, so Inertia keeps the
 * component mounted and only swaps the props — which left the form showing
 * the edit that had just been undone. The operator saw "reverted to its
 * Markdown seed" above a form still full of their own text, and saving again
 * would have quietly reapplied it.
 *
 * Re-reading the props whenever the server sends a new version of this page
 * fixes reverting and is right for saving too: after a save, the form should
 * show what was stored, not what was typed.
 */
watch(
    () => props.page,
    (next) => {
        /**
         * Except when the save was refused. A rejected save also comes back
         * as a fresh `page` prop — the stored version, which is precisely
         * what the operator was trying to change — so resyncing here would
         * answer "this field is wrong" by deleting everything they typed.
         */
        if (Object.keys(pageState.props.errors ?? {}).length > 0) {
            return;
        }

        payload.value = clonePayload();
        form.defaults({
            title: next.title,
            description: next.description,
            seo_title: next.seo_title,
            seo_description: next.seo_description,
            robots: next.robots,
            canonical_url: next.canonical_url,
            open_graph_image: next.open_graph_image,
        });
        form.reset();
    },
);

/**
 * The declaration decides what this form shows, in the order it declares it.
 * Metadata is separated from content because the two are stored differently —
 * columns and a JSON payload — and the server splits on the same list.
 */
const metaSchemaFields = computed<ContentField[]>(() =>
    props.schema.fields.filter((field) =>
        props.metaFields.includes(field.name),
    ),
);

const contentSchemaFields = computed<ContentField[]>(() =>
    props.schema.fields.filter(
        (field) => !props.metaFields.includes(field.name),
    ),
);

/**
 * The save runs a shape comparison against the other locale and refuses a
 * difference. Those come back as a list rather than one string, because "one
 * of your fields is wrong" is not something an operator can act on.
 */
const payloadErrors = computed<string[]>(() => {
    /**
     * Read from the shared page props rather than from `form.errors`.
     * `useForm` keys its error map by the fields it was constructed with, and
     * `payload` is not one of them — it is added by `transform()` at submit
     * time — so the refusal reached the browser and the operator saw
     * nothing: no save, no message.
     */
    const value = pageState.props.errors?.payload;

    if (Array.isArray(value)) {
        return value;
    }

    return value ? [value] : [];
});

/**
 * The guided pass, and the three cases it is for.
 *
 * A wizard is the wrong default surface here and the numbers say so: the
 * experience record alone has 64 leaf fields, and walking 64 questions to fix
 * one typo is worse than a form in every way. So the sectioned form is the
 * default, and a question sequence runs only where the operator genuinely
 * does not yet know what the page wants — a page never saved, a page being
 * authored for the first time, and a slot a developer has just added to the
 * declaration that no existing content fills.
 *
 * The third is the one that actually happens on a site this old, and it is
 * the reason this is generated rather than written: adding a field to
 * `app/Content/Schema` makes it appear here, unanswered, by itself.
 */
type GuidedStep = {
    path: string;
    label: string;
    help?: string;
};

function collectUnanswered(
    fields: ContentField[],
    value: JsonValue,
    trail: string[],
): GuidedStep[] {
    const steps: GuidedStep[] = [];

    for (const field of fields) {
        const here = [...trail, field.label || field.name];
        const held = isRecord(value) ? value[field.name] : undefined;

        if (field.repeats) {
            // An empty repeating field is a legitimate state — a section
            // family waiting for its first entry — so it is not a question.
            continue;
        }

        if (field.type === 'group') {
            steps.push(
                ...collectUnanswered(field.children ?? [], held ?? {}, here),
            );

            continue;
        }

        if (!field.required) {
            continue;
        }

        if (typeof held !== 'string' || held.trim() === '') {
            steps.push({
                path: here.join(' › '),
                label: field.label || field.name,
                help: field.help,
            });
        }
    }

    return steps;
}

function isRecord(value: JsonValue): value is JsonObject {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
}

const guidedSteps = computed<GuidedStep[]>(() =>
    collectUnanswered(contentSchemaFields.value, payload.value, []),
);

const guidedOpen = ref(false);

function metaValue(name: string): JsonValue {
    return (form as unknown as Record<string, JsonValue>)[name] ?? '';
}

function setMetaValue(name: string, value: JsonValue): void {
    (form as unknown as Record<string, JsonValue>)[name] =
        typeof value === 'string' ? value : '';
}

function submit(): void {
    form.transform((data) => ({ ...data, payload: payload.value })).put(
        `/admin/pages/${props.page.page_key}/${props.page.locale}`,
    );
}

function revert(): void {
    router.post(
        `/admin/pages/${props.page.page_key}/${props.page.locale}/revert`,
    );
}

/**
 * The preview is the real route, rendered from a saved draft — not a mock
 * inside the editor. For a site whose value is how it looks, a preview that
 * is not the page cannot answer the question being asked of it.
 */
function preview(): void {
    router.post(
        `/admin/pages/${props.page.page_key}/${props.page.locale}/preview`,
        { ...form.data(), payload: payload.value },
    );
}
</script>

<template>
    <AdminLayout>
        <AdminHead :title="`Edit ${page.page_key}`" />

        <form class="admin-page-edit" @submit.prevent="submit">
            <header class="admin-page-edit__header">
                <div>
                    <p class="type-eyebrow">{{ schema.label }}</p>
                    <h1 class="type-h1 admin-page-edit__title">
                        {{ page.page_key }} · {{ page.locale.toUpperCase() }}
                    </h1>
                    <p class="type-body admin-page-edit__copy">
                        Every field below is declared in
                        <code>app/Content/Schema</code>. Saving checks the
                        content against that declaration and against the other
                        language, and refuses a difference.
                    </p>
                </div>
                <div class="admin-page-edit__actions">
                    <span v-if="status" class="type-meta">{{ status }}</span>
                    <button
                        type="button"
                        class="admin-page-edit__revert"
                        @click="preview"
                    >
                        Preview
                    </button>
                    <button
                        v-if="hasSeed"
                        type="button"
                        class="admin-page-edit__revert"
                        @click="revert"
                    >
                        Revert to Markdown
                    </button>
                    <Button type="submit" :disabled="form.processing">
                        Save page
                    </Button>
                </div>
            </header>

            <details
                v-if="guidedSteps.length > 0"
                class="admin-page-edit__guided"
                :open="guidedOpen"
                @toggle="
                    guidedOpen = ($event.target as HTMLDetailsElement).open
                "
            >
                <summary class="admin-page-edit__guided-summary">
                    {{ guidedSteps.length }}
                    {{ guidedSteps.length === 1 ? 'field is' : 'fields are' }}
                    declared but not filled in
                </summary>
                <p class="type-body-sm admin-page-edit__guided-copy">
                    These are usually slots added to
                    <code>app/Content/Schema</code> after this page was written.
                    Each one is somewhere in the form below.
                </p>
                <ol class="admin-page-edit__guided-list">
                    <li v-for="step in guidedSteps" :key="step.path">
                        <span class="type-nav">{{ step.path }}</span>
                        <span v-if="step.help" class="type-meta">
                            {{ step.help }}
                        </span>
                    </li>
                </ol>
            </details>

            <p
                v-if="payloadErrors.length > 0"
                class="admin-page-edit__errors"
                role="alert"
            >
                <strong class="type-nav">This page was not saved.</strong>
                <span
                    v-for="error in payloadErrors"
                    :key="error"
                    class="type-body-sm"
                >
                    {{ error }}
                </span>
            </p>

            <div class="admin-page-edit__grid">
                <Panel class="admin-page-edit__panel" tone="elevated">
                    <p class="type-eyebrow">Metadata</p>
                    <SchemaField
                        v-for="field in metaSchemaFields"
                        :key="field.name"
                        :field="field"
                        :value="metaValue(field.name)"
                        :path="field.name"
                        @update:value="setMetaValue(field.name, $event)"
                    />
                </Panel>

                <Panel class="admin-page-edit__panel" tone="surface">
                    <p class="type-eyebrow">Content</p>
                    <SchemaField
                        v-for="field in contentSchemaFields"
                        :key="field.name"
                        :field="field"
                        :value="payload[field.name] ?? ''"
                        :path="field.name"
                        @update:value="
                            payload = { ...payload, [field.name]: $event }
                        "
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

.admin-page-edit__actions {
    flex-wrap: wrap;
    align-items: center;
}

.admin-page-edit__grid {
    grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
    align-items: start;
}

.admin-page-edit__panel {
    padding: 1rem;
    min-width: 0;
}

.admin-page-edit__revert {
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-sm);
    background: transparent;
    padding: 0.5rem 0.8rem;
    color: var(--sw-text-secondary);
    font: inherit;
    cursor: pointer;
}

.admin-page-edit__revert:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

.admin-page-edit__guided {
    border: 1px solid
        color-mix(
            in srgb,
            var(--sw-accent-sun, var(--sw-border)) 45%,
            var(--sw-border)
        );
    border-radius: var(--sw-radius-md);
    padding: var(--sw-space-3xs) var(--sw-space-xs);
}

.admin-page-edit__guided-summary {
    cursor: pointer;
    padding-block: var(--sw-space-4xs);
    color: var(--sw-text-primary);
    font-weight: 500;
}

.admin-page-edit__guided-summary:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

.admin-page-edit__guided-copy {
    margin: 0 0 var(--sw-space-3xs);
    color: var(--sw-text-secondary);
}

.admin-page-edit__guided-list {
    display: grid;
    gap: var(--sw-space-4xs);
    margin: 0;
    padding-inline-start: var(--sw-space-xs);
    color: var(--sw-text-secondary);
}

.admin-page-edit__guided-list li {
    display: grid;
    gap: 0.1em;
}

.admin-page-edit__errors {
    display: grid;
    gap: 0.35rem;
    margin: 0;
    border: 1px solid
        color-mix(in srgb, var(--sw-accent-coral) 55%, var(--sw-border));
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-accent-coral) 10%, transparent);
    padding: 0.85rem 1rem;
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
