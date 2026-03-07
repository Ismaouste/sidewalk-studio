<script setup lang="ts">
defineProps<{
    entries: Array<{
        field: string;
        label: string;
        message: string;
    }>;
}>();
</script>

<template>
    <div class="admin-settings-validation-summary">
        <p class="type-eyebrow">Validation summary</p>
        <ul class="admin-settings-validation-summary__list" role="alert">
            <li
                v-for="entry in entries"
                :key="entry.field"
                class="admin-settings-validation-summary__item"
            >
                <strong>{{ entry.label }}:</strong>
                <span>{{ entry.message }}</span>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.admin-settings-validation-summary {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-settings-validation-summary__list {
    display: grid;
    gap: 0.55rem;
    margin: 0;
    padding-left: 1.1rem;
}

.admin-settings-validation-summary__item {
    color: var(--sw-accent-coral);
}
</style>
