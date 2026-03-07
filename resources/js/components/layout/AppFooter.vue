<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
</script>

<template>
    <footer class="app-footer">
        <div class="sw-container app-footer__inner">
            <SectionDivider label="Public build log" />

            <div class="app-footer__content">
                <div class="app-footer__copy">
                    <p class="type-eyebrow">Sidewalk Studio</p>
                    <p class="app-footer__note">
                        Local-first Laravel portfolio. Consent-aware embeds.
                        Structured content. SSR-ready shell.
                    </p>
                </div>

                <div class="app-footer__actions">
                    <a
                        class="app-footer__link"
                        :href="`mailto:${page.props.site.contact.email}`"
                    >
                        {{ page.props.site.contact.email }}
                    </a>
                    <span class="type-meta">
                        {{ page.props.site.contact.location }}
                    </span>
                    <ConsentPreferencesButton />
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.app-footer {
    position: relative;
    z-index: 1;
    padding-bottom: var(--sw-space-lg);
}

.app-footer__inner {
    display: grid;
    gap: var(--sw-space-sm);
}

.app-footer__content {
    display: grid;
    gap: var(--sw-space-sm);
    border: 1px solid var(--sw-border);
    border-radius: var(--sw-radius-lg);
    background: color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    padding: var(--sw-space-sm);
    box-shadow: var(--sw-shadow-sm);
}

.app-footer__copy {
    display: grid;
    gap: var(--sw-space-3xs);
}

.app-footer__note {
    margin: 0;
    max-width: 40rem;
    color: var(--sw-text-secondary);
}

.app-footer__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
}

.app-footer__link {
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.2em;
}

@media (min-width: 768px) {
    .app-footer__content {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .app-footer__actions {
        justify-content: flex-end;
    }
}
</style>
