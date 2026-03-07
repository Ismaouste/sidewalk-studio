<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        href?: string;
        external?: boolean;
        variant?: 'primary' | 'secondary' | 'ghost';
        size?: 'sm' | 'md';
        type?: 'button' | 'submit' | 'reset';
        disabled?: boolean;
    }>(),
    {
        href: undefined,
        external: false,
        variant: 'primary',
        size: 'md',
        type: 'button',
        disabled: false,
    },
);

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

const isInternalLink = computed(
    () => Boolean(props.href) && !props.external && props.href?.startsWith('/'),
);

const component = computed(() => {
    if (isInternalLink.value) {
        return Link;
    }

    if (props.href) {
        return 'a';
    }

    return 'button';
});

const componentProps = computed(() => {
    if (isInternalLink.value) {
        return {
            href: props.href,
        };
    }

    if (props.href) {
        return {
            href: props.href,
            target: props.external ? '_blank' : undefined,
            rel: props.external ? 'noreferrer' : undefined,
            'aria-disabled': props.disabled || undefined,
        };
    }

    return {
        type: props.type,
        disabled: props.disabled,
    };
});
</script>

<template>
    <component
        :is="component"
        v-bind="componentProps"
        class="sw-button"
        :class="[`sw-button--${props.variant}`, `sw-button--${props.size}`]"
        @click="emit('click', $event)"
    >
        <slot />
    </component>
</template>

<style scoped>
.sw-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sw-space-3xs);
    border-radius: var(--sw-radius-md);
    font-family: var(--sw-font-body);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.2;
    transition:
        transform var(--sw-motion-fast),
        background-color var(--sw-motion-smooth),
        color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        box-shadow var(--sw-motion-fast);
}

.sw-button--md {
    min-height: 3rem;
    padding-inline: 1.2rem;
}

.sw-button--sm {
    min-height: 2.5rem;
    padding-inline: 0.95rem;
    font-size: 13px;
}

.sw-button--primary {
    border: 1px solid color-mix(in srgb, var(--sw-accent-sun) 82%, black 8%);
    background: var(--sw-accent-sun);
    color: var(--sw-text-inverse);
    box-shadow: var(--sw-shadow-sm);
}

.sw-button--secondary {
    border: 1px solid var(--sw-border);
    background: transparent;
    color: var(--sw-text-primary);
}

.sw-button--ghost {
    border: 0;
    padding-inline: 0;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-decoration-thickness: 1px;
    text-underline-offset: 0.22em;
}

.sw-button[disabled],
.sw-button[aria-disabled='true'] {
    opacity: 0.45;
    pointer-events: none;
}

.sw-button:active {
    transform: translateY(1px);
}

@media (hover: hover) {
    .sw-button:hover {
        transform: translateY(-1px);
    }

    .sw-button--primary:hover {
        background: color-mix(in srgb, var(--sw-accent-sun) 88%, white 12%);
    }

    .sw-button--secondary:hover {
        border-color: var(--sw-accent-dominant);
        color: var(--sw-accent-dominant);
    }
}
</style>
