<script setup lang="ts">
import SectionIntro from '@/components/design-system/SectionIntro.vue';

defineProps<{
    eyebrow: string;
    thesis: string;
    summary: string;
}>();
</script>

<template>
    <div class="manifesto-opener">
        <SectionIntro
            :eyebrow="eyebrow"
            :title="thesis"
            :description="summary"
            size="hero"
        >
            <template v-if="$slots.actions" #actions>
                <slot name="actions" />
            </template>
        </SectionIntro>
    </div>
</template>

<style scoped>
.manifesto-opener {
    view-transition-name: page-hero;
    contain: layout;
    position: relative;
    padding: var(--sw-space-sm) 0;
    isolation: isolate;
}

.manifesto-opener::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background: radial-gradient(
        ellipse at 18% 12%,
        color-mix(in oklch, var(--sw-twilight-glow) 40%, transparent),
        transparent 58%
    );
    pointer-events: none;
    border-radius: var(--sw-radius-lg);
}

@supports (view-transition-name: none) {
    @media (prefers-reduced-motion: no-preference) {
        .manifesto-opener {
            opacity: 1;
            transform: translateY(0);
            transition:
                opacity 600ms cubic-bezier(0.22, 1, 0.36, 1),
                transform 600ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        @starting-style {
            .manifesto-opener {
                opacity: 0;
                transform: translateY(8px);
            }
        }
    }
}

@media (prefers-reduced-motion: reduce) {
    .manifesto-opener {
        transition: none;
    }
}
</style>
