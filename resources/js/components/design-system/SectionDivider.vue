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
    padding-block: 6px 2px;
}

.section-divider::before,
.section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: color-mix(in srgb, var(--sw-border) 82%, transparent);
}

.section-divider__label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding-inline: 0.15rem;
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, var(--sw-text-primary));
    font-size: 12px;
    letter-spacing: 0.16em;
}

.section-divider__label::before {
    content: '•';
    color: color-mix(in srgb, var(--sw-accent-dominant) 70%, var(--sw-accent-sun));
    font-size: 0.9em;
}
</style>
