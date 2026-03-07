import * as CookieConsent from 'vanilla-cookieconsent';
import '@orestbida/iframemanager/dist/iframemanager.js';
import 'vanilla-cookieconsent/dist/cookieconsent.css';
import '@orestbida/iframemanager/dist/iframemanager.css';
import {
    getEmbedsByCategory,
    registerEmbed,
    registerScript,
    syncScripts,
} from '@/lib/consent-registry';
import type { ConsentConfig } from '@/types';

let initialized = false;

function registerDefaults(driver: ConsentConfig['driver']) {
    registerEmbed('youtube', 'media', {
        embedUrl: 'https://www.youtube-nocookie.com/embed/{data-id}',
        thumbnailUrl: 'https://i3.ytimg.com/vi/{data-id}/hqdefault.jpg',
        iframe: {
            allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
        },
        languages: {
            en: {
                notice: 'This embed is hosted by YouTube. Loading it means accepting the media category and YouTube terms for this session.',
                loadBtn: 'Load video',
                loadAllBtn: 'Always allow YouTube',
            },
        },
    });

    registerScript({
        key: 'analytics-driver',
        category: 'analytics',
        load: () => {
            if (driver === 'none') {
                return;
            }

            window.dispatchEvent(
                new CustomEvent('sidewalk:analytics:enabled', {
                    detail: { driver },
                }),
            );
        },
        unload: () => {
            window.dispatchEvent(
                new CustomEvent('sidewalk:analytics:disabled'),
            );
        },
    });
}

function syncConsentState() {
    syncScripts('analytics', CookieConsent.acceptedCategory('analytics'));
    syncScripts('media', CookieConsent.acceptedCategory('media'));
}

export async function initializeConsent(config: ConsentConfig): Promise<void> {
    if (initialized) {
        return;
    }

    registerDefaults(config.driver);

    const iframeManager = window.iframemanager?.();

    if (iframeManager) {
        iframeManager.run({
            currLang: 'en',
            onChange: ({
                changedServices,
                eventSource,
            }: {
                changedServices: string[];
                eventSource: { type: string };
            }) => {
                if (
                    eventSource.type === 'click' &&
                    changedServices.length > 0
                ) {
                    CookieConsent.acceptService(changedServices, 'media');
                }
            },
            services: getEmbedsByCategory('media'),
        });
    }

    await CookieConsent.run({
        guiOptions: {
            consentModal: {
                layout: 'box wide',
                position: 'bottom right',
                equalWeightButtons: false,
            },
            preferencesModal: {
                layout: 'box',
                position: 'right',
                equalWeightButtons: false,
            },
        },
        categories: {
            necessary: {
                enabled: true,
                readOnly: true,
            },
            analytics: {},
            media: {},
        },
        language: {
            default: 'en',
            translations: {
                en: {
                    consentModal: {
                        title: 'Privacy is part of the architecture',
                        description:
                            'Sidewalk Studio loads only what is needed by default. Analytics stays off and embeds remain blocked until you opt in.',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Keep strict mode',
                        showPreferencesBtn: 'Manage preferences',
                    },
                    preferencesModal: {
                        title: 'Consent preferences',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Reject optional',
                        savePreferencesBtn: 'Save choices',
                        closeIconLabel: 'Close',
                        sections: [
                            {
                                title: 'Why these choices exist',
                                description:
                                    'The repo uses consent categories as a technical boundary: nothing external should load before an explicit choice exists.',
                            },
                            {
                                title: 'Necessary',
                                description:
                                    'Required for the consent state and core application delivery. These cannot be disabled.',
                                linkedCategory: 'necessary',
                            },
                            {
                                title: 'Analytics',
                                description:
                                    'Reserved for later privacy-first measurement adapters. In v0 the default driver is a no-op placeholder.',
                                linkedCategory: 'analytics',
                            },
                            {
                                title: 'Media',
                                description:
                                    'Allows iframe-based services such as YouTube to load only after explicit approval.',
                                linkedCategory: 'media',
                            },
                        ],
                    },
                },
            },
        },
        onFirstConsent: syncConsentState,
        onConsent: syncConsentState,
        onChange: syncConsentState,
    });

    window.SidewalkConsent = {
        showPreferences: () => CookieConsent.showPreferences(),
    };

    initialized = true;
}
