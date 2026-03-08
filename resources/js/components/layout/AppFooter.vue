<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import SectionDivider from '@/components/design-system/SectionDivider.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              dataLabel: 'Traitement des données',
              contactLabel: 'Contact direct',
              themeLabel: 'Thème',
              consentNote:
                  'Analytics et heatmaps doivent rester opt-in explicite.',
          }
        : {
              dataLabel: 'Data processing',
              contactLabel: 'Direct contact',
              themeLabel: 'Theme',
              consentNote: 'Analytics and heatmaps must remain explicit opt-in.',
          },
);
</script>

<template>
    <footer class="app-footer">
        <div class="sw-container app-footer__inner">
            <SectionDivider :label="page.props.site.shell.footerDividerLabel" />

            <div class="app-footer__content">
                <div class="app-footer__copy">
                    <p class="type-eyebrow">Sidewalk Studio</p>
                    <p class="app-footer__note">
                        {{ page.props.site.shell.footerNote }}
                    </p>
                    <p class="type-meta app-footer__consent-note">
                        {{ copy.consentNote }}
                    </p>
                </div>

                <div class="app-footer__actions">
                    <div class="app-footer__links">
                        <a
                            class="app-footer__link"
                            :href="`mailto:${page.props.site.contact.email}`"
                        >
                            {{ copy.contactLabel }}
                        </a>
                        <a
                            class="app-footer__link"
                            href="/data-processing"
                            rel="nofollow"
                        >
                            {{ copy.dataLabel }}
                        </a>
                    </div>

                    <div class="app-footer__meta">
                        <div class="app-footer__contact">
                            <a
                                class="app-footer__mail"
                                :href="`mailto:${page.props.site.contact.email}`"
                            >
                                {{ page.props.site.contact.email }}
                            </a>
                            <span class="type-meta app-footer__location">
                                {{ page.props.site.contact.location }}
                            </span>
                        </div>

                        <div class="app-footer__controls">
                            <span class="type-nav app-footer__theme-label">
                                {{ copy.themeLabel }}
                            </span>
                            <ThemeToggle compact />
                            <ConsentPreferencesButton />
                        </div>
                    </div>
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
    backdrop-filter: blur(18px);
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

.app-footer__consent-note {
    margin: 0;
}

.app-footer__actions {
    display: grid;
    gap: var(--sw-space-xs);
    align-items: start;
}

.app-footer__links,
.app-footer__contact,
.app-footer__controls {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sw-space-2xs);
    align-items: center;
}

.app-footer__meta {
    display: grid;
    gap: var(--sw-space-xs);
}

.app-footer__link {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.18em;
}

.app-footer__mail {
    display: inline-flex;
    align-items: center;
    color: var(--sw-accent-dominant);
    text-decoration: none;
}

.app-footer__location {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
}

.app-footer__theme-label {
    color: var(--sw-text-muted);
}

@media (hover: hover) {
    .app-footer__link:hover,
    .app-footer__mail:hover {
        color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 78%,
            var(--sw-accent-sun)
        );
    }
}

@media (min-width: 768px) {
    .app-footer__content {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
    }

    .app-footer__actions {
        justify-items: end;
    }

    .app-footer__meta {
        justify-items: end;
    }
}
</style>
