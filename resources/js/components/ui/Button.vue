<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { localizePublicHref, resolvePublicHref } from '@/lib/publicHref';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();

const props = withDefaults(
    defineProps<{
        href?: string;
        external?: boolean;
        variant?: 'primary' | 'secondary' | 'ghost';
        size?: 'sm' | 'md';
        type?: 'button' | 'submit' | 'reset';
        disabled?: boolean;
        arrow?: boolean;
        externalMark?: boolean;
        rel?: string;
        target?: string;
    }>(),
    {
        href: undefined,
        external: false,
        variant: 'primary',
        size: 'md',
        type: 'button',
        disabled: false,
        arrow: false,
        externalMark: false,
        rel: undefined,
        target: undefined,
    },
);

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

const isInternalLink = computed(
    () => Boolean(props.href) && !props.external && props.href?.startsWith('/'),
);
const resolvedHref = computed(() => {
    if (!props.href) {
        return undefined;
    }

    const localizedHref = isInternalLink.value
        ? localizePublicHref(props.href, page.props.site.locale)
        : props.href;

    return resolvePublicHref(
        localizedHref,
        page.props.site.runtime.staticPreview,
        page.props.site.runtime.staticBasePath,
    );
});

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
            href: resolvedHref.value,
        };
    }

    if (props.href) {
        return {
            href: resolvedHref.value,
            target: props.target ?? (props.external ? '_blank' : undefined),
            rel: props.rel ?? (props.external ? 'noreferrer' : undefined),
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
        :class="[
            `sw-button--${props.variant}`,
            `sw-button--${props.size}`,
            {
                'sw-button--with-arrow': props.arrow || props.externalMark,
            },
        ]"
        @click="emit('click', $event)"
    >
        <span class="sw-button__label">
            <slot />
        </span>
        <span
            v-if="props.arrow || props.externalMark"
            class="sw-button__arrow"
            :class="{ 'sw-button__arrow--external': props.externalMark }"
            aria-hidden="true"
        >
            <template v-if="props.externalMark">↗</template>
            <template v-else>→</template>
        </span>
    </component>
</template>

<style scoped>
.sw-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sw-space-3xs);
    border-radius: var(--sw-radius-md);
    font-family:
        var(--sw-font-body),
        'Apple Color Emoji',
        'Segoe UI Emoji',
        'Noto Color Emoji';
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

.sw-button__label {
    display: inline-flex;
    align-items: center;
}

.sw-button__arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 0.8rem;
    font-size: 0.98em;
    line-height: 1;
    transform: translateY(-0.02em);
}

.sw-button__arrow--external {
    font-size: 0.9em;
    transform: translateY(-0.14em);
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
    border: 1px solid var(--sw-button-primary-border);
    background: var(--sw-button-primary-bg);
    color: var(--sw-button-primary-text);
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
    justify-content: flex-start;
    text-decoration: none;
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

    .sw-button--with-arrow:hover .sw-button__arrow {
        animation: sw-button-arrow-jiggle 0.72s ease;
    }

    .sw-button--primary:hover {
        background: var(--sw-button-primary-bg-hover);
    }

    .sw-button--secondary:hover {
        border-color: var(--sw-accent-dominant);
        color: var(--sw-accent-dominant);
    }
}

@keyframes sw-button-arrow-jiggle {
    0%,
    100% {
        transform: translate(0, -0.02em);
    }

    30% {
        transform: translate(0.14rem, -0.02em);
    }

    55% {
        transform: translate(0.03rem, -0.02em);
    }

    78% {
        transform: translate(0.18rem, -0.02em);
    }
}
</style>
