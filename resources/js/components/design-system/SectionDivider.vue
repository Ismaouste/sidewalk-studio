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
    gap: var(--sw-space-2xs);
    padding-block: 10px 4px;
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
    font-size: 11px;
    letter-spacing: 0.18em;
}
</style>
