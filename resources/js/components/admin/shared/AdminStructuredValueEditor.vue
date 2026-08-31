<script setup lang="ts">
import { computed } from 'vue';
import Panel from '@/components/ui/Panel.vue';
import type { JsonObject, JsonValue } from '@/types';

const props = defineProps<{
    label: string;
    value: JsonValue;
}>();

const emit = defineEmits<{
    'update:value': [value: JsonValue];
}>();

defineOptions({
    name: 'AdminStructuredValueEditor',
});

/**
 * The template iterates these rather than `value` directly: v-for over a union
 * makes Vue infer the key as `number | "valueOf"`, picking up Object.prototype
 * members. Narrowing once in script keeps the markup honestly typed.
 */
const objectEntries = computed<JsonObject | null>(() =>
    isObject(props.value) ? props.value : null,
);

const arrayItems = computed<JsonValue[] | null>(() =>
    Array.isArray(props.value) ? props.value : null,
);

function currentObject(): JsonObject {
    return isObject(props.value) ? props.value : {};
}

function currentArray(): JsonValue[] {
    return Array.isArray(props.value) ? props.value : [];
}

function updateObjectEntry(key: string, value: JsonValue) {
    emit('update:value', { ...currentObject(), [key]: value });
}

function updateArrayEntry(index: number, value: JsonValue) {
    const items = [...currentArray()];
    items[index] = value;
    emit('update:value', items);
}

function removeArrayEntry(index: number) {
    const items = [...currentArray()];
    items.splice(index, 1);
    emit('update:value', items);
}

function addScalarArrayEntry() {
    emit('update:value', [...currentArray(), '']);
}

function addObjectArrayEntry() {
    const items = currentArray();
    emit('update:value', [...items, firstObjectTemplate(items)]);
}

/**
 * New rows in an object array copy the shape of the first existing row with
 * every leaf blanked, so the editor never invents keys the backend does not
 * already accept.
 */
function firstObjectTemplate(items: JsonValue[]): JsonObject {
    const firstObject = items.find((item) => isObject(item));

    if (!firstObject) {
        return {};
    }

    return Object.fromEntries(
        Object.entries(firstObject).map(([key, value]) => [
            key,
            defaultForValue(value),
        ]),
    );
}

function defaultForValue(value: JsonValue): JsonValue {
    if (Array.isArray(value)) {
        return [];
    }

    if (isObject(value)) {
        return Object.fromEntries(
            Object.entries(value).map(([key, item]) => [
                key,
                defaultForValue(item),
            ]),
        );
    }

    if (typeof value === 'boolean') {
        return false;
    }

    if (typeof value === 'number') {
        return 0;
    }

    return '';
}

function updateScalar(value: string | boolean) {
    emit('update:value', value);
}

function isObject(value: JsonValue): value is JsonObject {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function isScalarArray(value: JsonValue): boolean {
    return (
        Array.isArray(value) &&
        value.every((item) => !isObject(item) && !Array.isArray(item))
    );
}

function isObjectArray(value: JsonValue): boolean {
    return Array.isArray(value) && value.every((item) => isObject(item));
}

function shouldUseTextarea(value: JsonValue): boolean {
    return (
        typeof value === 'string' &&
        (value.includes('\n') || value.length > 110)
    );
}

function prettify(key: string): string {
    return key
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (match) => match.toUpperCase());
}
</script>

<template>
    <div class="structured-editor">
        <template v-if="objectEntries">
            <div class="structured-editor__group">
                <div
                    v-for="(entryValue, entryKey) in objectEntries"
                    :key="`${label}-${String(entryKey)}`"
                    class="structured-editor__entry"
                >
                    <AdminStructuredValueEditor
                        :label="prettify(String(entryKey))"
                        :value="entryValue"
                        @update:value="
                            updateObjectEntry(String(entryKey), $event)
                        "
                    />
                </div>
            </div>
        </template>

        <template v-else-if="arrayItems && isScalarArray(value)">
            <div class="structured-editor__array">
                <div
                    v-for="(entryValue, index) in arrayItems"
                    :key="`${label}-${index}`"
                    class="structured-editor__array-row"
                >
                    <input
                        v-if="typeof entryValue !== 'boolean'"
                        :value="String(entryValue ?? '')"
                        class="structured-editor__input"
                        type="text"
                        @input="
                            updateArrayEntry(
                                index,
                                ($event.target as HTMLInputElement).value,
                            )
                        "
                    />
                    <label v-else class="structured-editor__toggle">
                        <input
                            :checked="entryValue"
                            type="checkbox"
                            @change="
                                updateArrayEntry(
                                    index,
                                    ($event.target as HTMLInputElement).checked,
                                )
                            "
                        />
                        <span>{{ label }} {{ index + 1 }}</span>
                    </label>
                    <button
                        class="structured-editor__remove"
                        type="button"
                        @click="removeArrayEntry(index)"
                    >
                        Remove
                    </button>
                </div>
                <button
                    class="structured-editor__add"
                    type="button"
                    @click="addScalarArrayEntry"
                >
                    Add item
                </button>
            </div>
        </template>

        <template v-else-if="arrayItems && isObjectArray(value)">
            <div class="structured-editor__array">
                <Panel
                    v-for="(entryValue, index) in arrayItems"
                    :key="`${label}-${index}`"
                    class="structured-editor__panel"
                    tone="grid"
                >
                    <div class="structured-editor__panel-header">
                        <p class="type-nav">{{ label }} {{ index + 1 }}</p>
                        <button
                            class="structured-editor__remove"
                            type="button"
                            @click="removeArrayEntry(index)"
                        >
                            Remove
                        </button>
                    </div>
                    <AdminStructuredValueEditor
                        :label="`${label} ${index + 1}`"
                        :value="entryValue"
                        @update:value="updateArrayEntry(index, $event)"
                    />
                </Panel>
                <button
                    class="structured-editor__add"
                    type="button"
                    @click="addObjectArrayEntry"
                >
                    Add block
                </button>
            </div>
        </template>

        <template v-else-if="typeof value === 'boolean'">
            <label class="structured-editor__toggle">
                <input
                    :checked="value"
                    type="checkbox"
                    @change="
                        updateScalar(
                            ($event.target as HTMLInputElement).checked,
                        )
                    "
                />
                <span>{{ label }}</span>
            </label>
        </template>

        <template v-else>
            <label class="structured-editor__field">
                <span class="type-nav">{{ label }}</span>
                <textarea
                    v-if="shouldUseTextarea(value)"
                    class="structured-editor__input structured-editor__input--textarea"
                    rows="4"
                    :value="String(value ?? '')"
                    @input="
                        updateScalar(
                            ($event.target as HTMLTextAreaElement).value,
                        )
                    "
                />
                <input
                    v-else
                    class="structured-editor__input"
                    type="text"
                    :value="String(value ?? '')"
                    @input="
                        updateScalar(($event.target as HTMLInputElement).value)
                    "
                />
            </label>
        </template>
    </div>
</template>

<style scoped>
.structured-editor,
.structured-editor__group,
.structured-editor__array {
    display: grid;
    gap: 0.9rem;
}

.structured-editor__field,
.structured-editor__entry {
    display: grid;
    gap: 0.45rem;
}

.structured-editor__input {
    min-height: 3rem;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: 0.8rem 0.95rem;
}

.structured-editor__input--textarea {
    min-height: 7rem;
    resize: vertical;
}

.structured-editor__array-row,
.structured-editor__panel-header {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    justify-content: space-between;
}

.structured-editor__panel {
    display: grid;
    gap: 0.9rem;
    padding: 1rem;
}

.structured-editor__toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
}

.structured-editor__remove,
.structured-editor__add {
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    padding: 0.55rem 0.8rem;
    font-size: 0.9rem;
}

.structured-editor__add {
    justify-self: start;
}
</style>
