<script setup lang="ts">
import { computed } from 'vue';
import type { ContentField, JsonObject, JsonValue } from '@/types';

/**
 * One declared field, rendered.
 *
 * The component is recursive because the declaration is: a group holds
 * fields, one of which may be a repeating group of groups. `experience`
 * reaches three levels deep and the editor follows it without knowing
 * anything about experience.
 *
 * The repeating-group case is the one that decides whether this is usable on
 * a phone. `home.focus_areas` is three items times six fields — eighteen
 * inputs — and `experience.professional_sections` is heavier still. Every
 * repeating group in this content already carries a human-readable
 * identifier, so the declaration names it (`itemLabel`) and each item
 * collapses into a `<details>` whose `<summary>` is that value. The operator
 * sees three named rows and opens one.
 *
 * `<details>` rather than a JavaScript accordion: it is a platform primitive,
 * it is keyboard- and screen-reader-correct without help, and it survives
 * having its state changed by find-in-page.
 */
const props = defineProps<{
    field: ContentField;
    value: JsonValue;
    path: string;
}>();

const emit = defineEmits<{
    'update:value': [value: JsonValue];
}>();

defineOptions({ name: 'SchemaField' });

const label = computed(() => props.field.label || props.field.name);

const items = computed<JsonValue[]>(() =>
    Array.isArray(props.value) ? props.value : [],
);

const group = computed<JsonObject>(() =>
    isObject(props.value) ? props.value : {},
);

function isObject(value: JsonValue): value is JsonObject {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
}

/** Multi-line inputs for the types that hold prose. */
const isProse = computed(
    () => props.field.type === 'text' || props.field.type === 'markdown',
);

const inputType = computed(() =>
    props.field.type === 'date' ? 'date' : 'text',
);

/**
 * What an item of a repeating group is called in its own `<summary>`. Falls
 * back to a position, because an item whose label has not been filled in yet
 * still has to be findable.
 */
function summaryFor(item: JsonValue, index: number): string {
    const key = props.field.itemLabel;

    if (key && isObject(item)) {
        const candidate = item[key];

        if (typeof candidate === 'string' && candidate.trim() !== '') {
            return candidate;
        }
    }

    return `${label.value} ${index + 1}`;
}

function updateGroupChild(name: string, next: JsonValue): void {
    emit('update:value', { ...group.value, [name]: next });
}

function updateItem(index: number, next: JsonValue): void {
    const copy = [...items.value];
    copy[index] = next;
    emit('update:value', copy);
}

function removeItem(index: number): void {
    const copy = [...items.value];
    copy.splice(index, 1);
    emit('update:value', copy);
}

function moveItem(index: number, offset: number): void {
    const target = index + offset;

    if (target < 0 || target >= items.value.length) {
        return;
    }

    // Lift and reinsert rather than swap by index: `noUncheckedIndexedAccess`
    // types every element read as possibly undefined, and a destructuring
    // swap has no way to say the two indices were just bounds-checked.
    const copy = [...items.value];
    const [moved] = copy.splice(index, 1);

    if (moved === undefined) {
        return;
    }

    copy.splice(target, 0, moved);
    emit('update:value', copy);
}

/**
 * A new item is built from the declaration rather than left empty, so it
 * arrives with the shape the save path is going to require. An operator
 * adding a section should not have to discover its fields by being rejected.
 */
function blankFor(field: ContentField): JsonValue {
    if (field.repeats) {
        return [];
    }

    if (field.type === 'group') {
        const next: JsonObject = {};

        for (const child of field.children ?? []) {
            next[child.name] = blankFor(child);
        }

        return next;
    }

    if (field.type === 'choice') {
        return field.choices?.[0] ?? '';
    }

    return '';
}

function addItem(): void {
    emit('update:value', [
        ...items.value,
        blankFor({ ...props.field, repeats: false }),
    ]);
}
</script>

<template>
    <!-- A repeating group: one <details> per item, summarised by its label. -->
    <fieldset
        v-if="field.repeats && field.type === 'group'"
        class="schema-field"
    >
        <legend class="type-nav schema-field__legend">
            {{ label }}
            <span class="schema-field__count">{{ items.length }}</span>
        </legend>
        <p v-if="field.help" class="type-meta schema-field__help">
            {{ field.help }}
        </p>

        <details
            v-for="(item, index) in items"
            :key="`${path}.${index}`"
            class="schema-field__item"
        >
            <summary class="schema-field__summary">
                {{ summaryFor(item, index) }}
            </summary>

            <div class="schema-field__item-body">
                <SchemaField
                    v-for="child in field.children ?? []"
                    :key="child.name"
                    :field="child"
                    :value="isObject(item) ? (item[child.name] ?? '') : ''"
                    :path="`${path}.${index}.${child.name}`"
                    @update:value="
                        updateItem(index, {
                            ...(isObject(item) ? item : {}),
                            [child.name]: $event,
                        })
                    "
                />

                <div class="schema-field__item-actions">
                    <button
                        type="button"
                        class="schema-field__button"
                        :disabled="index === 0"
                        @click="moveItem(index, -1)"
                    >
                        Move up
                    </button>
                    <button
                        type="button"
                        class="schema-field__button"
                        :disabled="index === items.length - 1"
                        @click="moveItem(index, 1)"
                    >
                        Move down
                    </button>
                    <button
                        type="button"
                        class="schema-field__button schema-field__button--danger"
                        @click="removeItem(index)"
                    >
                        Remove
                    </button>
                </div>
            </div>
        </details>

        <button type="button" class="schema-field__button" @click="addItem">
            Add {{ label.toLowerCase() }}
        </button>
    </fieldset>

    <!-- A repeating scalar: a list of one-line or prose inputs. -->
    <fieldset v-else-if="field.repeats" class="schema-field">
        <legend class="type-nav schema-field__legend">
            {{ label }}
            <span class="schema-field__count">{{ items.length }}</span>
        </legend>
        <p v-if="field.help" class="type-meta schema-field__help">
            {{ field.help }}
        </p>

        <div
            v-for="(item, index) in items"
            :key="`${path}.${index}`"
            class="schema-field__row"
        >
            <textarea
                v-if="isProse"
                class="schema-field__input schema-field__input--prose"
                rows="3"
                :value="typeof item === 'string' ? item : ''"
                @input="
                    updateItem(
                        index,
                        ($event.target as HTMLTextAreaElement).value,
                    )
                "
            />
            <input
                v-else
                class="schema-field__input"
                :type="inputType"
                :value="typeof item === 'string' ? item : ''"
                @input="
                    updateItem(index, ($event.target as HTMLInputElement).value)
                "
            />
            <button
                type="button"
                class="schema-field__button schema-field__button--danger"
                @click="removeItem(index)"
            >
                Remove
            </button>
        </div>

        <button type="button" class="schema-field__button" @click="addItem">
            Add {{ label.toLowerCase() }}
        </button>
    </fieldset>

    <!-- A group: a fieldset of its children. -->
    <fieldset v-else-if="field.type === 'group'" class="schema-field">
        <legend class="type-nav schema-field__legend">{{ label }}</legend>
        <p v-if="field.help" class="type-meta schema-field__help">
            {{ field.help }}
        </p>

        <SchemaField
            v-for="child in field.children ?? []"
            :key="child.name"
            :field="child"
            :value="group[child.name] ?? ''"
            :path="`${path}.${child.name}`"
            @update:value="updateGroupChild(child.name, $event)"
        />
    </fieldset>

    <!-- One of a fixed set. -->
    <label v-else-if="field.type === 'choice'" class="schema-field__label">
        <span class="type-nav">{{ label }}</span>
        <select
            class="schema-field__input"
            :value="typeof value === 'string' ? value : ''"
            @change="
                emit('update:value', ($event.target as HTMLSelectElement).value)
            "
        >
            <option v-for="choice in field.choices ?? []" :key="choice">
                {{ choice }}
            </option>
        </select>
        <span v-if="field.help" class="type-meta schema-field__help">
            {{ field.help }}
        </span>
    </label>

    <!-- A single value. -->
    <label v-else class="schema-field__label">
        <span class="type-nav">
            {{ label }}
            <span v-if="!field.required" class="schema-field__optional">
                optional
            </span>
        </span>
        <textarea
            v-if="isProse"
            class="schema-field__input schema-field__input--prose"
            :rows="field.type === 'markdown' ? 12 : 3"
            :value="typeof value === 'string' ? value : ''"
            @input="
                emit(
                    'update:value',
                    ($event.target as HTMLTextAreaElement).value,
                )
            "
        />
        <input
            v-else
            class="schema-field__input"
            :type="inputType"
            :value="typeof value === 'string' ? value : ''"
            @input="
                emit('update:value', ($event.target as HTMLInputElement).value)
            "
        />
        <span v-if="field.help" class="type-meta schema-field__help">
            {{ field.help }}
        </span>
    </label>
</template>

<style scoped>
.schema-field,
.schema-field__label {
    display: grid;
    gap: var(--sw-space-3xs);
    min-width: 0;
}

.schema-field {
    gap: var(--sw-space-xs);
    margin: 0;
    border: 1px solid color-mix(in srgb, var(--sw-border) 60%, transparent);
    border-radius: var(--sw-radius-md);
    padding: var(--sw-space-xs);
}

.schema-field__legend {
    display: flex;
    align-items: center;
    gap: var(--sw-space-3xs);
    padding-inline: var(--sw-space-3xs);
    color: var(--sw-text-primary);
}

.schema-field__count {
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sw-border) 40%, transparent);
    padding: 0 0.4em;
    color: var(--sw-text-muted);
    font-variant-numeric: tabular-nums;
}

.schema-field__optional {
    color: var(--sw-text-muted);
    font-weight: 400;
}

.schema-field__help {
    margin: 0;
    color: var(--sw-text-muted);
}

.schema-field__input {
    min-height: var(--sw-space-md);
    width: 100%;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: var(--sw-space-4xs) var(--sw-space-3xs);
    color: inherit;
    font: inherit;
}

/**
 * Prose grows to fit what it holds.
 *
 * A fixed `rows` clips a paragraph at three lines and hides the rest behind
 * an inner scrollbar, which on a phone means an operator editing a sentence
 * they cannot see. `field-sizing: content` is the platform answering this
 * without a resize observer or an input handler — the repo's stated
 * preference — and browsers without it simply keep the old fixed height,
 * which is where this started.
 *
 * The ceiling matters as much as the growth: one long paragraph should not
 * push every other field off the screen.
 */
.schema-field__input--prose {
    min-height: var(--sw-space-lg);
    max-height: 60svh;
    resize: vertical;
    field-sizing: content;
}

.schema-field__input:focus-visible {
    outline: none;
    border-color: var(--sw-border-focus);
}

.schema-field__item {
    border: 1px solid color-mix(in srgb, var(--sw-border) 60%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-elevated) 60%, transparent);
}

.schema-field__summary {
    cursor: pointer;
    padding: var(--sw-space-3xs) var(--sw-space-xs);
    color: var(--sw-text-primary);
    font-weight: 500;
    overflow-wrap: anywhere;
}

.schema-field__summary:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: -2px;
}

.schema-field__item-body {
    display: grid;
    gap: var(--sw-space-xs);
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 50%, transparent);
    padding: var(--sw-space-xs);
}

.schema-field__row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    gap: var(--sw-space-3xs);
}

.schema-field__item-actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-3xs);
}

.schema-field__button {
    justify-self: start;
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sw-bg-base) 92%, transparent);
    padding: var(--sw-space-4xs) var(--sw-space-3xs);
    color: inherit;
    font: inherit;
    cursor: pointer;
}

.schema-field__button:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.schema-field__button--danger {
    border-color: color-mix(
        in srgb,
        var(--sw-accent-coral) 60%,
        var(--sw-border)
    );
    color: var(--sw-accent-coral);
}

.schema-field__button:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

/**
 * The narrow breakpoint, where this editor is meant to be usable.
 *
 * Two changes, both measured rather than guessed. The remove button stops
 * sharing a row with the field it removes — a full touch target beside a
 * full-width input leaves neither usable — and every control grows to 44px,
 * which is WCAG 2.5.5. Measured at 390px they came out at 37 and 38: past
 * the 24px minimum, short of comfortable, and the `<summary>` is *the*
 * affordance on this surface, since it is what turns eighteen inputs into
 * three rows an operator can scan.
 */
@media (max-width: 560px) {
    .schema-field__row {
        grid-template-columns: minmax(0, 1fr);
    }

    /**
     * 44px is WCAG 2.5.5, not a spacing decision, so it is a literal rather
     * than a `--sw-space-*` token that happens to be close to it today.
     */
    .schema-field__summary,
    .schema-field__button {
        display: flex;
        align-items: center;
        min-height: 44px;
    }

    .schema-field__input {
        min-height: 44px;
    }

    .schema-field__item-actions .schema-field__button {
        flex: 1 1 auto;
        justify-content: center;
    }
}
</style>
