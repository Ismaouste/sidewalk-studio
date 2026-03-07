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
                    <div class="app-footer__contact">
                        <a
                            class="app-footer__link"
                            :href="`mailto:${page.props.site.contact.email}`"
                        >
                            {{ page.props.site.contact.email }}
                        </a>
                        <span class="type-meta app-footer__location">
                            {{ page.props.site.contact.location }}
                        </span>
                    </div>
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
    padding-block: var(--sw-space-md) var(--sw-space-lg);
}

.app-footer__inner {
    display: grid;
    gap: var(--sw-space-md);
}

.app-footer__content {
    position: relative;
    display: grid;
    gap: var(--sw-space-sm);
    border: 1px solid var(--sw-border);
    border-radius: calc(var(--sw-radius-lg) + 2px);
    background:
        linear-gradient(
            180deg,
            color-mix(in srgb, var(--sw-bg-elevated) 22%, transparent),
            transparent 48%
        ),
        color-mix(in srgb, var(--sw-bg-surface) 92%, transparent);
    padding: clamp(18px, 2.6vw, var(--sw-space-sm));
    box-shadow: var(--sw-shadow-md);
}

.app-footer__content::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 1px 0 color-mix(in srgb, white 18%, transparent);
    pointer-events: none;
}

.app-footer__copy {
    display: grid;
    gap: 6px;
}

.app-footer__note {
    margin: 0;
    max-width: 40rem;
    color: var(--sw-text-secondary);
}

.app-footer__actions {
    display: grid;
    gap: var(--sw-space-xs);
    align-items: start;
}

.app-footer__contact {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
}

.app-footer__link {
    display: inline-flex;
    align-items: center;
    min-height: 2.5rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 88%, transparent);
    border-radius: var(--sw-radius-full);
    background: color-mix(in srgb, var(--sw-bg-elevated) 78%, transparent);
    padding-inline: 0.9rem;
    color: var(--sw-accent-dominant);
    text-decoration: none;
    box-shadow: var(--sw-shadow-sm);
    transition:
        background-color var(--sw-motion-fast),
        border-color var(--sw-motion-fast),
        color var(--sw-motion-fast),
        transform var(--sw-motion-fast);
}

.app-footer__location {
    display: inline-flex;
    align-items: center;
    min-height: 2.5rem;
}

@media (hover: hover) {
    .app-footer__link:hover {
        transform: translateY(-1px);
        border-color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 28%,
            var(--sw-border)
        );
        background: color-mix(in srgb, var(--sw-bg-elevated) 92%, transparent);
    }
}

@media (min-width: 768px) {
    .app-footer__content {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .app-footer__actions {
        justify-items: end;
    }
}
</style>
