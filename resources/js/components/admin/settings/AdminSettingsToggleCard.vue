<script setup lang="ts">
const props = defineProps<{
    modelValue: boolean;
    label: string;
    description: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
}>();

function update(value: boolean) {
    emit('update:modelValue', value);
}
</script>

<template>
    <label
        class="admin-settings-toggle-card"
        :class="{
            'admin-settings-toggle-card--checked': props.modelValue,
        }"
    >
        <input
            :checked="props.modelValue"
            type="checkbox"
            @change="update(($event.target as HTMLInputElement).checked)"
        />
        <div>
            <span class="type-nav">{{ props.label }}</span>
            <p class="type-body-sm admin-settings-toggle-card__copy">
                {{ props.description }}
            </p>
        </div>
        <span class="type-meta admin-settings-toggle-card__state">
            {{ props.modelValue ? 'Enabled' : 'Hidden' }}
        </span>
    </label>
</template>

<style scoped>
.admin-settings-toggle-card {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    gap: 0.75rem;
    align-items: start;
    border: 1px solid color-mix(in srgb, var(--sw-border) 78%, transparent);
    border-radius: var(--sw-radius-md);
    background: color-mix(in srgb, var(--sw-bg-base) 78%, transparent);
    padding: 0.9rem 1rem;
    transition:
        border-color var(--sw-motion-fast),
        transform var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.admin-settings-toggle-card--checked {
    border-color: color-mix(in srgb, var(--sw-accent-green) 42%, transparent);
    background: color-mix(in srgb, var(--sw-accent-green) 10%, transparent);
}

.admin-settings-toggle-card input {
    margin-top: 0.2rem;
}

.admin-settings-toggle-card__copy {
    color: var(--sw-text-secondary);
}

.admin-settings-toggle-card__state {
    border-radius: 999px;
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    padding: 0.35rem 0.65rem;
    color: var(--sw-text-secondary);
}

@media (hover: hover) {
    .admin-settings-toggle-card:hover {
        border-color: var(--sw-accent-dominant);
        transform: translateY(-1px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .admin-settings-toggle-card {
        transition: none;
    }
}

@media (max-width: 720px) {
    .admin-settings-toggle-card {
        grid-template-columns: auto minmax(0, 1fr);
    }

    .admin-settings-toggle-card__state {
        justify-self: start;
    }
}
</style>
