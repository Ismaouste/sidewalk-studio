<script setup lang="ts">
defineProps<{
    label: string;
}>();
</script>

<template>
    <div class="section-divider" aria-hidden="true">
        <span class="type-nav section-divider__label">{{ label }}</span>
    </div>
</template>

<style scoped>
.section-divider {
    display: flex;
    align-items: center;
    gap: var(--sw-space-xs);
    padding-block: var(--sw-space-sm);
}

.section-divider::before,
.section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--sw-border);
}

.section-divider__label {
    color: var(--sw-text-muted);
}
</style>
