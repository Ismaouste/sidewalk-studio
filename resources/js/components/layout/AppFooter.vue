<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ConsentPreferencesButton from '@/components/ConsentPreferencesButton.vue';
import AccessibilityPanel from '@/components/layout/AccessibilityPanel.vue';
import LocaleSwitcher from '@/components/layout/LocaleSwitcher.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import Cartouche from '@/components/sheet/Cartouche.vue';
import { useDecodedEmail } from '@/composables/useDecodedEmail';
import { copy as copyTree } from '@/copy';
import { localizePublicHref } from '@/lib/publicHref';
import type { SiteProps } from '@/types';

const page = usePage<{ site: SiteProps }>();
const { mailto } = useDecodedEmail(() => page.props.site.contact.email);
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
/**
 * The signature used to hold its own copy of the name and the email, three
 * elements away from the ones this footer already reads off `site`. Two
 * copies of an identity in one component is one too many: the settings won.
 */
const repositoryUrl = computed(() => page.props.site.repositoryUrl);
const dataProcessingHref = computed(() =>
    localizePublicHref('/data-processing', page.props.site.locale),
);
const colophonHref = computed(() =>
    localizePublicHref('/colophon', page.props.site.locale),
);
const copy = computed(() => copyTree[page.props.site.locale].layout.footer);

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
            <Cartouche />
            <div class="app-footer__content">
                <div class="app-footer__copy">
                    <p class="app-footer__note">
                        {{ page.props.site.shell.footerNote }}
                    </p>
                    <p
                        v-if="isStaticPreview"
                        class="type-meta app-footer__consent-note"
                    >
                        {{ copy.staticPreviewNote }}
                    </p>
                </div>

                <div class="app-footer__actions">
                    <div class="app-footer__links">
                        <a
                            class="app-footer__link"
                            :href="dataProcessingHref"
                            rel="nofollow"
                        >
                            {{ copy.dataLabel }}
                        </a>
                        <a class="app-footer__link" :href="colophonHref">
                            {{ copy.colophonLabel }}
                        </a>
                        <button
                            type="button"
                            class="app-footer__link app-footer__link--button"
                            @click="backToTop"
                        >
                            {{ copy.backToTopLabel }}
                        </button>
                        <span
                            class="app-footer__pipe"
                            aria-hidden="true"
                        ></span>
                        <a
                            v-if="linkedinUrl"
                            class="app-footer__social"
                            :href="linkedinUrl"
                            target="_blank"
                            rel="noreferrer"
                        >
                            <svg
                                class="app-footer__social-icon"
                                viewBox="0 0 16 16"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <path
                                    d="M1.5 5.5h3v9h-3zM3 1.5a1.75 1.75 0 1 1 0 3.5 1.75 1.75 0 0 1 0-3.5zM6.5 5.5h2.9v1.3h.04c.4-.76 1.4-1.56 2.88-1.56 3.08 0 3.65 2.03 3.65 4.67v4.59h-3v-4.07c0-.97-.02-2.22-1.35-2.22-1.35 0-1.56 1.06-1.56 2.15v4.14h-3z"
                                />
                            </svg>
                            {{ copy.linkedinLabel }}
                        </a>
                        <a class="app-footer__social" :href="mailto()">
                            <svg
                                class="app-footer__social-icon"
                                viewBox="0 0 16 16"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <path
                                    d="M1.5 3h13a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm.5 1.6v6.9h12V4.6L8 9 2 4.6zM3.1 4 8 7.6 12.9 4z"
                                />
                            </svg>
                            {{ copy.mailLabel }}
                        </a>
                    </div>

                    <div class="app-footer__meta">
                        <div class="app-footer__controls">
                            <LocaleSwitcher />
                            <ThemeToggle compact />
                            <AccessibilityPanel />
                            <ConsentPreferencesButton v-if="!isStaticPreview" />
                        </div>
                    </div>
                </div>
            </div>

            <p v-if="page.props.site.colophonQuote" class="app-footer__quote">
                <span class="app-footer__quote-text">
                    « {{ page.props.site.colophonQuote.text }} »
                </span>
                <span
                    v-if="page.props.site.colophonQuote.author"
                    class="app-footer__quote-author"
                >
                    — {{ page.props.site.colophonQuote.author }}
                </span>
            </p>

            <div class="app-footer__legal">
                <a
                    class="app-footer__legal-link"
                    :href="repositoryUrl"
                    target="_blank"
                    rel="noreferrer nofollow"
                >
                    {{ copy.licenseLabel }}
                </a>
                <span class="app-footer__legal-link">{{
                    page.props.site.name
                }}</span>
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

.app-footer__quote {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: baseline;
    margin: 0;
    padding: var(--sw-space-3xs) 0;
    color: color-mix(
        in srgb,
        var(--sw-text-muted) 80%,
        var(--sw-text-secondary)
    );
    font-style: italic;
    text-wrap: pretty;
    max-width: 56rem;
}

.app-footer__quote-text {
    font-family: var(--sw-font-display);
    font-size: 0.86rem;
    line-height: 1.45;
    color: color-mix(
        in srgb,
        var(--sw-text-secondary) 78%,
        var(--sw-text-primary)
    );
}

.app-footer__quote-author {
    font-family: var(--sw-font-heading);
    font-size: 0.66rem;
    font-style: normal;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sw-text-muted);
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

/* Where to find me: after a thin vertical rule, small, monochrome, and not styled like the plain links. */
.app-footer__pipe {
    display: none;
    align-self: stretch;
    width: 1px;
    min-height: 1.1rem;
    background: color-mix(in srgb, var(--sw-border) 90%, transparent);
}

.app-footer__social {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    min-height: 1.8rem;
    padding-inline: 0.6rem;
    border: 1px solid color-mix(in srgb, var(--sw-border) 90%, transparent);
    color: var(--sw-text-primary);
    font-family: var(--sw-font-code);
    font-size: 0.68rem;
    letter-spacing: 0.06em;
    text-decoration: none;
    text-transform: uppercase;
}

.app-footer__social-icon {
    width: 0.85rem;
    height: 0.85rem;
    fill: currentColor;
}

@media (hover: hover) {
    .app-footer__link:hover,
    .app-footer__legal-link:hover {
        color: color-mix(
            in srgb,
            var(--sw-accent-dominant) 78%,
            var(--sw-accent-sun)
        );
    }
}

@media (min-width: 640px) {
    .app-footer__pipe {
        display: block;
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
        border-left: 1px solid
            color-mix(in srgb, var(--sw-border) 76%, transparent);
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
