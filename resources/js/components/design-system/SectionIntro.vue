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
    gap: var(--sw-space-xs);
    max-width: 52rem;
}

.section-intro--hero {
    gap: var(--sw-space-sm);
}

.section-intro__eyebrow {
    width: fit-content;
}

.section-intro__title {
    margin: 0;
    color: var(--sw-text-primary);
    text-wrap: balance;
}

.section-intro__description {
    margin: 0;
    max-width: 42rem;
    color: var(--sw-text-secondary);
}

.section-intro__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-xs);
    padding-top: var(--sw-space-xs);
}

.section-intro__extra {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    padding-top: var(--sw-space-3xs);
}
</style>
