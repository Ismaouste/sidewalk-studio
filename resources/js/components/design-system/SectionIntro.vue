<script setup lang="ts">
const props = withDefaults(
    defineProps<{
        eyebrow?: string;
        title: string;
        description?: string;
        size?: 'default' | 'hero';
    }>(),
    {
        eyebrow: undefined,
        description: undefined,
        size: 'default',
    },
);
</script>

<template>
    <header class="section-intro" :class="`section-intro--${props.size}`">
        <p v-if="props.eyebrow" class="type-eyebrow section-intro__eyebrow">
            {{ props.eyebrow }}
        </p>
        <component
            :is="props.size === 'hero' ? 'h1' : 'h2'"
            class="section-intro__title"
            :class="props.size === 'hero' ? 'type-display-xl' : 'type-h1'"
        >
            {{ props.title }}
        </component>
        <p
            v-if="props.description"
            class="section-intro__description"
            :class="props.size === 'hero' ? 'type-body-lg' : 'type-body'"
        >
            {{ props.description }}
        </p>
        <div v-if="$slots.actions" class="section-intro__actions">
            <slot name="actions" />
        </div>
        <div v-if="$slots.default" class="section-intro__extra">
            <slot />
        </div>
    </header>
</template>

<style scoped>
.section-intro {
    display: grid;
    gap: clamp(14px, 2.4vw, var(--sw-space-xs));
    max-width: 54rem;
}

.section-intro--hero {
    gap: clamp(var(--sw-space-xs), 3.4vw, var(--sw-space-sm));
    max-width: 58rem;
}

.section-intro__eyebrow {
    width: fit-content;
    margin: 0;
}

.section-intro:not(.section-intro--hero) .section-intro__eyebrow {
    color: color-mix(in srgb, var(--sw-text-secondary) 84%, var(--sw-text-primary));
    font-size: 12px;
    letter-spacing: 0.16em;
}

.section-intro__title {
    margin: 0;
    color: var(--sw-text-primary);
    text-wrap: balance;
}

.section-intro__description {
    margin: 0;
    max-width: 44rem;
    color: var(--sw-text-secondary);
    text-wrap: pretty;
}

.section-intro__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: clamp(var(--sw-space-3xs), 1.8vw, var(--sw-space-xs));
}

.section-intro__extra {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 6px;
}

@media (max-width: 640px) {
    .section-intro {
        gap: var(--sw-space-3xs);
    }

    .section-intro--hero {
        gap: var(--sw-space-xs);
    }

    .section-intro__actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: var(--sw-space-3xs);
        padding-top: var(--sw-space-3xs);
    }

    .section-intro__actions :deep(.sw-button) {
        width: 100%;
    }

    .section-intro__extra {
        gap: var(--sw-space-3xs);
        padding-top: 0;
    }
}
</style>
