<script setup lang="ts">
defineOptions({
    name: 'AdminStructuredValueEditor',
});

import Panel from '@/components/ui/Panel.vue';

const props = defineProps<{
    label: string;
    value: unknown;
}>();

const emit = defineEmits<{
    'update:value': [value: unknown];
}>();

function updateObjectEntry(key: string, value: unknown) {
    emit('update:value', {
        ...(props.value as Record<string, unknown>),
        [key]: value,
    });
}

function updateArrayEntry(index: number, value: unknown) {
    const items = [...((props.value as unknown[]) ?? [])];
    items[index] = value;
    emit('update:value', items);
}

function removeArrayEntry(index: number) {
    const items = [...((props.value as unknown[]) ?? [])];
    items.splice(index, 1);
    emit('update:value', items);
}

function addScalarArrayEntry() {
    emit('update:value', [...((props.value as unknown[]) ?? []), '']);
}

function addObjectArrayEntry() {
    const items = ((props.value as unknown[]) ?? []);
    const template = firstObjectTemplate(items);
    emit('update:value', [...items, template]);
}

function firstObjectTemplate(items: unknown[]) {
    const firstObject = items.find((item) => isObject(item)) as Record<string, unknown> | undefined;

    if (!firstObject) {
        return {};
    }

    return Object.fromEntries(
        Object.entries(firstObject).map(([key, value]) => [key, defaultForValue(value)]),
    );
}

function defaultForValue(value: unknown): unknown {
    if (Array.isArray(value)) {
        return [];
    }

    if (isObject(value)) {
        return Object.fromEntries(
            Object.entries(value).map(([key, item]) => [key, defaultForValue(item)]),
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

function isObject(value: unknown): value is Record<string, unknown> {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function isScalarArray(value: unknown): boolean {
    return Array.isArray(value) && value.every((item) => !isObject(item) && !Array.isArray(item));
}

function isObjectArray(value: unknown): boolean {
    return Array.isArray(value) && value.every((item) => isObject(item));
}

function shouldUseTextarea(value: unknown): boolean {
    return typeof value === 'string' && (value.includes('\n') || value.length > 110);
}

function prettify(key: string): string {
    return key
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (match) => match.toUpperCase());
}
</script>

<template>
    <div class="structured-editor">
        <template v-if="isObject(value)">
            <div class="structured-editor__group">
                <div
                    v-for="(entryValue, entryKey) in value"
                    :key="`${label}-${String(entryKey)}`"
                    class="structured-editor__entry"
                >
                    <AdminStructuredValueEditor
                        :label="prettify(String(entryKey))"
                        :value="entryValue"
                        @update:value="updateObjectEntry(String(entryKey), $event)"
                    />
                </div>
            </div>
        </template>

        <template v-else-if="isScalarArray(value)">
            <div class="structured-editor__array">
                <div
                    v-for="(entryValue, index) in value"
                    :key="`${label}-${index}`"
                    class="structured-editor__array-row"
                >
                    <input
                        v-if="typeof entryValue !== 'boolean'"
                        :value="String(entryValue ?? '')"
                        class="structured-editor__input"
                        type="text"
                        @input="updateArrayEntry(index, ($event.target as HTMLInputElement).value)"
                    />
                    <label v-else class="structured-editor__toggle">
                        <input
                            :checked="entryValue"
                            type="checkbox"
                            @change="updateArrayEntry(index, ($event.target as HTMLInputElement).checked)"
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
                <button class="structured-editor__add" type="button" @click="addScalarArrayEntry">
                    Add item
                </button>
            </div>
        </template>

        <template v-else-if="isObjectArray(value)">
            <div class="structured-editor__array">
                <Panel
                    v-for="(entryValue, index) in value"
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
                <button class="structured-editor__add" type="button" @click="addObjectArrayEntry">
                    Add block
                </button>
            </div>
        </template>

        <template v-else-if="typeof value === 'boolean'">
            <label class="structured-editor__toggle">
                <input
                    :checked="value"
                    type="checkbox"
                    @change="updateScalar(($event.target as HTMLInputElement).checked)"
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
                    @input="updateScalar(($event.target as HTMLTextAreaElement).value)"
                />
                <input
                    v-else
                    class="structured-editor__input"
                    type="text"
                    :value="String(value ?? '')"
                    @input="updateScalar(($event.target as HTMLInputElement).value)"
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
