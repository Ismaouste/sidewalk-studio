<script setup lang="ts">
import Panel from '@/components/ui/Panel.vue';

const props = withDefaults(
    defineProps<{
        id?: string;
        tone?: 'surface' | 'grid' | 'elevated';
        eyebrow: string;
        title: string;
        copy?: string;
    }>(),
    {
        id: undefined,
        tone: 'surface',
        copy: undefined,
    },
);
</script>

<template>
    <Panel :id="props.id" class="admin-settings-section" :tone="props.tone">
        <div class="admin-settings-section__intro">
            <p class="type-eyebrow">{{ props.eyebrow }}</p>
            <h2 class="type-h3 admin-settings-section__title">
                {{ props.title }}
            </h2>
            <p
                v-if="props.copy"
                class="type-body-sm admin-settings-section__copy"
            >
                {{ props.copy }}
            </p>
        </div>

        <slot />
    </Panel>
</template>

<style scoped>
.admin-settings-section {
    display: grid;
    gap: var(--sw-space-sm);
    padding: clamp(var(--sw-space-sm), 3vw, var(--sw-space-md));
    scroll-margin-top: 150px;
}

.admin-settings-section__intro {
    display: grid;
    gap: 0.35rem;
}

.admin-settings-section__title {
    margin: 0;
}

.admin-settings-section__copy {
    max-width: 62ch;
    color: var(--sw-text-secondary);
}
</style>
