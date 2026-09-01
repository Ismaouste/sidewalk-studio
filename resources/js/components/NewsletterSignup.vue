<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Panel from '@/components/ui/Panel.vue';
import { copy as copyTree } from '@/copy';
import { capture } from '@/lib/analytics';
import type { SiteProps } from '@/types';

const props = defineProps<{
    segment: 'engineering' | 'local-business';
    context: 'home' | 'journal' | 'case-study';
}>();

const page = usePage<{ site: SiteProps }>();
const copy = computed(
    () => copyTree[page.props.site.locale].content.newsletter,
);
const contextCopy = computed(() => copy.value.contexts[props.context]);

const email = ref('');
const honeypot = ref('');
const state = ref<'idle' | 'submitting' | 'pending' | 'error'>('idle');

async function subscribe(): Promise<void> {
    if (state.value === 'submitting') {
        return;
    }

    state.value = 'submitting';

    try {
        const response = await fetch('/newsletter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                email: email.value,
                segment: props.segment,
                locale: page.props.site.locale,
                company_website: honeypot.value,
            }),
        });

        if (!response.ok) {
            state.value = 'error';
            return;
        }

        state.value = 'pending';
        capture('lead_intent', {
            channel: 'newsletter',
            funnel_stage: 'V3',
            segment: props.segment,
        });
    } catch {
        state.value = 'error';
    }
}
</script>

<template>
    <Panel class="newsletter-signup" tone="grid">
        <p class="type-eyebrow">{{ contextCopy.eyebrow }}</p>
        <h2 class="type-h3 newsletter-signup__title">
            {{ contextCopy.title }}
        </h2>
        <p class="type-body-sm newsletter-signup__summary">
            {{ contextCopy.summary }}
        </p>

        <p
            v-if="state === 'pending'"
            class="type-body-sm newsletter-signup__pending"
            role="status"
        >
            {{ copy.pendingNote }}
        </p>

        <form
            v-else
            class="newsletter-signup__form"
            @submit.prevent="subscribe"
        >
            <label
                class="type-meta newsletter-signup__label"
                :for="`newsletter-${context}`"
            >
                {{ copy.emailLabel }}
            </label>
            <div class="newsletter-signup__row">
                <input
                    :id="`newsletter-${context}`"
                    v-model="email"
                    class="newsletter-signup__input"
                    type="email"
                    name="email"
                    required
                    autocomplete="email"
                    :placeholder="copy.emailPlaceholder"
                />
                <Button
                    type="submit"
                    size="sm"
                    :disabled="state === 'submitting'"
                >
                    {{
                        state === 'submitting'
                            ? copy.submittingCta
                            : copy.submitCta
                    }}
                </Button>
            </div>

            <input
                v-model="honeypot"
                class="newsletter-signup__trap"
                type="text"
                name="company_website"
                tabindex="-1"
                autocomplete="off"
                aria-hidden="true"
            />

            <p
                v-if="state === 'error'"
                class="type-body-sm newsletter-signup__error"
            >
                {{ copy.errorNote }}
            </p>
            <p class="type-meta newsletter-signup__privacy">
                {{ copy.privacyNote }}
            </p>
        </form>
    </Panel>
</template>

<style scoped>
.newsletter-signup {
    display: grid;
    gap: var(--sw-space-xs);
    padding: var(--sw-space-sm);
}

.newsletter-signup__title,
.newsletter-signup__summary,
.newsletter-signup__pending,
.newsletter-signup__error,
.newsletter-signup__privacy {
    margin: 0;
}

.newsletter-signup__summary {
    color: var(--sw-text-secondary);
}

.newsletter-signup__form {
    display: grid;
    gap: var(--sw-space-2xs);
}

.newsletter-signup__row {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
}

.newsletter-signup__input {
    flex: 1 1 12rem;
    min-width: 0;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: var(--sw-radius-sm);
    background: color-mix(in srgb, var(--sw-bg-elevated) 88%, transparent);
    color: var(--sw-text-primary);
    font: inherit;
    padding: var(--sw-space-2xs) var(--sw-space-xs);
    transition:
        border-color var(--sw-motion-fast),
        background-color var(--sw-motion-fast);
}

.newsletter-signup__input::placeholder {
    color: var(--sw-text-muted);
}

.newsletter-signup__input:focus-visible {
    outline: 2px solid var(--sw-border-focus);
    outline-offset: 2px;
}

/*
 * The honeypot stays in the accessibility tree's shadow: removed from tab
 * order and hidden from every real reader, but present for form-filling
 * bots that answer every field they can see in the DOM.
 */
.newsletter-signup__trap {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
}

.newsletter-signup__privacy {
    color: var(--sw-text-muted);
}

.newsletter-signup__error {
    color: var(--sw-accent-dominant);
}
</style>
