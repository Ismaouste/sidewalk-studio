<script setup lang="ts">
defineProps<{
    items: ReadonlyArray<{
        href: string;
        label: string;
    }>;
}>();
</script>

<template>
    <div class="admin-settings-jump-nav">
        <p class="type-eyebrow">Section guide</p>
        <div class="admin-settings-jump-nav__grid">
            <a
                v-for="item in items"
                :key="item.href"
                class="admin-settings-jump-nav__link"
                :href="item.href"
            >
                {{ item.label }}
            </a>
        </div>
    </div>
</template>

<style scoped>
.admin-settings-jump-nav {
    display: grid;
    gap: var(--sw-space-sm);
}

.admin-settings-jump-nav__grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.admin-settings-jump-nav__link {
    border-radius: 999px;
    border: 1px solid color-mix(in srgb, var(--sw-border) 80%, transparent);
    background: color-mix(in srgb, var(--sw-bg-base) 76%, transparent);
    padding: 0.45rem 0.75rem;
    font-size: 0.9rem;
    color: var(--sw-text-primary);
    transition:
        border-color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

@media (hover: hover) {
    .admin-settings-jump-nav__link:hover {
        border-color: var(--sw-accent-dominant);
        transform: translateY(-1px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .admin-settings-jump-nav__link {
        transition: none;
    }
}
</style>
