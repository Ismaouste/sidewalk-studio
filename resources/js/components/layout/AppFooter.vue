<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AccessibilityPanel from '@/components/layout/AccessibilityPanel.vue';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import LocaleSwitcher from '@/components/layout/LocaleSwitcher.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const isStaticPreview = computed(() => page.props.site.runtime.staticPreview);
const linkedinUrl = computed(() => {
    const value = page.props.site.social.linkedin_url?.trim() ?? '';

    if (
        value === '' ||
        value === 'https://www.linkedin.com' ||
        value === 'https://linkedin.com'
    ) {
        return null;
    }

    return value;
});
const footerSignature = {
    license: '© MIT',
    licenseHref: 'https://github.com/Ismaouste/sidewalk-studio',
    name: 'Ismaël Rodmacq',
    email: 'ismael@rodmacq.com',
};
const copy = computed(() =>
    page.props.site.locale === 'fr'
        ? {
              dataLabel: 'Traitement des données',
              contactLabel: 'Mail',
              linkedinLabel: 'LinkedIn',
              backToTopLabel: 'Retour en haut',
              consentNote: 'Mesure d’audience en opt-in explicite.',
              staticPreviewNote:
                  'Preview statique : formulaire et préférences avancées désactivés.',
          }
        : {
              dataLabel: 'Data processing',
              contactLabel: 'Direct contact',
              linkedinLabel: 'LinkedIn',
              backToTopLabel: 'Back to top',
              consentNote: 'Analytics stays explicit opt-in.',
              staticPreviewNote:
                  'Static preview: form handling and advanced preferences are disabled.',
          },
);

function backToTop(): void {
    if (typeof window === 'undefined') {
        return;
    }

    const prefersReducedMotion =
        document.documentElement.getAttribute('data-motion') === 'reduced';

    window.scrollTo({
        top: 0,
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
    });
}
</script>

<template>
    <footer class="app-footer">
        <div class="sw-container app-footer__inner">
            <div class="app-footer__content">
                <div class="app-footer__copy">
                    <p class="type-eyebrow app-footer__brand">
                        Sidewalk Studio
                    </p>
                    <p class="app-footer__note">
                        {{ page.props.site.shell.footerNote }}
                    </p>
                    <p class="type-meta app-footer__consent-note">
                        {{
                            isStaticPreview
                                ? copy.staticPreviewNote
                                : copy.consentNote
                        }}
                    </p>
                </div>

                <div class="app-footer__actions">
                    <div class="app-footer__links">
                        <a
                            v-if="linkedinUrl"
                            class="app-footer__link"
                            :href="linkedinUrl"
                            target="_blank"
                            rel="noreferrer"
                        >
                            {{ copy.linkedinLabel }}
                        </a>
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
                        <button
                            type="button"
                            class="app-footer__link app-footer__link--button"
                            @click="backToTop"
                        >
                            {{ copy.backToTopLabel }}
                        </button>
                    </div>

                    <div class="app-footer__meta">
                        <div class="app-footer__contact">
                            <span class="type-meta app-footer__location">
                                {{ page.props.site.contact.location }}
                            </span>
                            <a
                                class="app-footer__mail"
                                :href="`mailto:${page.props.site.contact.email}`"
                            >
                                {{ page.props.site.contact.email }}
                            </a>
                        </div>

                        <div class="app-footer__controls">
                            <LocaleSwitcher />
                            <ThemeToggle compact />
                            <AccessibilityPanel />
                            <ConsentPreferencesButton
                                v-if="!isStaticPreview"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-footer__legal">
                <a
                    class="app-footer__legal-link"
                    :href="footerSignature.licenseHref"
                    target="_blank"
                    rel="noreferrer nofollow"
                >
                    {{ footerSignature.license }}
                </a>
                <a
                    class="app-footer__legal-link"
                    :href="`mailto:${footerSignature.email}`"
                    rel="nofollow"
                >
                    {{ footerSignature.name }}
                </a>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.app-footer {
    position: relative;
    z-index: 1;
    padding-block: var(--sw-space-xs) var(--sw-space-lg);
}

.app-footer__inner {
    display: grid;
    gap: 0;
}

.app-footer__content {
    position: relative;
    display: grid;
    gap: var(--sw-space-xs);
    padding-block: clamp(18px, 2.8vw, var(--sw-space-sm));
    border-top: 1px solid color-mix(in srgb, var(--sw-border) 82%, transparent);
}

.app-footer__copy {
    display: grid;
    gap: 6px;
}

.app-footer__brand {
    margin: 0;
    color: color-mix(in srgb, var(--sw-text-primary) 80%, var(--sw-accent-sun));
}

.app-footer__note {
    margin: 0;
    max-width: 42rem;
    color: var(--sw-text-secondary);
}

.app-footer__consent-note {
    margin: 0;
}

.app-footer__actions {
    display: grid;
    gap: 10px;
    align-items: start;
}

.app-footer__legal {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 9px;
    align-items: center;
    justify-content: flex-end;
    padding-top: 8px;
    color: color-mix(in srgb, var(--sw-text-muted) 82%, transparent);
    font-family: var(--sw-font-code);
    font-size: 0.68rem;
    letter-spacing: 0.04em;
}

.app-footer__legal-link {
    color: inherit;
    text-decoration: none;
}

.app-footer__links,
.app-footer__contact,
.app-footer__controls {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 10px;
    align-items: center;
}

.app-footer__meta {
    display: grid;
    gap: 8px;
}

.app-footer__link {
    display: inline-flex;
    align-items: center;
    min-height: 1.8rem;
    color: var(--sw-accent-dominant);
    text-decoration: underline;
    text-underline-offset: 0.18em;
}

.app-footer__link--button {
    border: 0;
    background: transparent;
    padding: 0;
    font: inherit;
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
    min-height: 1.8rem;
}

@media (hover: hover) {
    .app-footer__link:hover,
    .app-footer__mail:hover,
    .app-footer__legal-link:hover {
        color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 78%,
            var(--sw-accent-sun)
        );
    }
}

@media (min-width: 768px) {
    .app-footer__content {
        grid-template-columns: minmax(0, 1.15fr) auto;
        align-items: start;
        column-gap: clamp(28px, 4vw, var(--sw-space-lg));
    }

    .app-footer__actions {
        justify-items: end;
        padding-left: clamp(20px, 3vw, var(--sw-space-md));
        border-left: 1px solid color-mix(in srgb, var(--sw-border) 76%, transparent);
    }

    .app-footer__meta {
        justify-items: end;
    }

    .app-footer__legal {
        justify-content: flex-end;
    }
}

.app-footer :deep(.locale-switcher) {
    min-height: 2rem;
    padding: 2px;
    background: color-mix(in srgb, var(--sw-bg-surface) 40%, transparent);
    box-shadow: none;
}

.app-footer :deep(.locale-switcher__option) {
    min-width: 1.9rem;
    min-height: 1.55rem;
    padding-inline: 0.45rem;
    font-size: 8px;
}

.app-footer :deep(.theme-toggle--compact) {
    min-height: 2rem;
    border-color: color-mix(in srgb, var(--sw-border) 84%, transparent);
    background: color-mix(in srgb, var(--sw-bg-surface) 34%, transparent);
    box-shadow: none;
    backdrop-filter: none;
}

.app-footer :deep(.theme-toggle--compact .theme-toggle__thumb) {
    top: 2px;
    bottom: 2px;
    left: 2px;
    width: calc(50% - 2px);
    box-shadow: none;
}

.app-footer :deep(.theme-toggle--compact .theme-toggle__option) {
    min-width: 3.9rem;
    min-height: calc(2rem - 4px);
    font-size: 8px;
}

.app-footer :deep(.consent-preferences-button) {
    min-height: 2rem;
    padding-inline: 0.72rem;
    font-size: 12px;
    border-color: color-mix(in srgb, var(--sw-border) 84%, transparent);
    background: transparent;
}
</style>
