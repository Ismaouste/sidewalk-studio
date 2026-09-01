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

type ConsentLocale = 'en' | 'fr';

const consentTranslations = {
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
                        'Product analytics through PostHog, hosted in the EU. Used to understand which pages and offers get read. Session replay and heatmaps are a separate, explicit switch on the data-processing page — never part of "Accept all".',
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
        youtube: {
            notice: 'This embed is hosted by YouTube. Loading it means accepting the media category and YouTube terms for this session.',
            loadBtn: 'Load video',
            loadAllBtn: 'Always allow YouTube',
        },
    },
    fr: {
        consentModal: {
            title: "La vie privée fait partie de l'architecture",
            description:
                "Sidewalk Studio ne charge que le strict nécessaire par défaut. Les analytics restent coupés et les embeds sont bloqués tant que vous n'avez pas choisi.",
            acceptAllBtn: 'Tout accepter',
            acceptNecessaryBtn: 'Rester en mode strict',
            showPreferencesBtn: 'Gérer mes choix',
        },
        preferencesModal: {
            title: 'Préférences de consentement',
            acceptAllBtn: 'Tout accepter',
            acceptNecessaryBtn: 'Refuser le facultatif',
            savePreferencesBtn: 'Enregistrer mes choix',
            closeIconLabel: 'Fermer',
            sections: [
                {
                    title: 'Pourquoi ces choix existent',
                    description:
                        "Le site utilise les catégories de consentement comme frontière technique : rien d'externe ne doit se charger sans choix explicite.",
                },
                {
                    title: 'Nécessaire',
                    description:
                        "Requis pour l'état du consentement et le fonctionnement essentiel du site. Cette catégorie ne peut pas être désactivée.",
                    linkedCategory: 'necessary',
                },
                {
                    title: 'Analytics',
                    description:
                        "Mesure d'audience produit via PostHog, hébergé dans l'Union européenne. Sert à comprendre quelles pages et offres sont lues. La relecture de session et les cartes de chaleur ont leur propre interrupteur explicite sur la page traitement des données — jamais inclus dans « Tout accepter ».",
                    linkedCategory: 'analytics',
                },
                {
                    title: 'Médias',
                    description:
                        'Autorise le chargement de services externes basés sur des iframes, comme YouTube, uniquement après accord explicite.',
                    linkedCategory: 'media',
                },
            ],
        },
        youtube: {
            notice: 'Cet embed est hébergé par YouTube. Le charger revient à accepter la catégorie média et les conditions de YouTube pour cette session.',
            loadBtn: 'Charger la vidéo',
            loadAllBtn: 'Toujours autoriser YouTube',
        },
    },
} satisfies Record<
    ConsentLocale,
    {
        consentModal: Record<string, string>;
        preferencesModal: {
            title: string;
            acceptAllBtn: string;
            acceptNecessaryBtn: string;
            savePreferencesBtn: string;
            closeIconLabel: string;
            sections: Array<{
                title: string;
                description: string;
                linkedCategory?: 'necessary' | 'analytics' | 'media';
            }>;
        };
        youtube: {
            notice: string;
            loadBtn: string;
            loadAllBtn: string;
        };
    }
>;

function resolveConsentLocale(locale?: string): ConsentLocale {
    return locale === 'fr' ? 'fr' : 'en';
}

function registerDefaults(config: ConsentConfig) {
    registerEmbed('youtube', 'media', {
        embedUrl: 'https://www.youtube-nocookie.com/embed/{data-id}',
        thumbnailUrl: 'https://i3.ytimg.com/vi/{data-id}/hqdefault.jpg',
        iframe: {
            allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
        },
        languages: {
            en: {
                ...consentTranslations.en.youtube,
            },
            fr: {
                ...consentTranslations.fr.youtube,
            },
        },
    });

    registerScript({
        key: 'analytics-driver',
        category: 'analytics',
        load: async () => {
            if (config.driver === 'none') {
                return;
            }

            const { enableAnalytics } = await import('@/lib/analytics');
            enableAnalytics(config);
        },
        unload: () => {
            void import('@/lib/analytics').then(({ disableAnalytics }) => {
                disableAnalytics();
            });
        },
    });
}

function syncConsentState() {
    syncScripts('analytics', CookieConsent.acceptedCategory('analytics'));
    syncScripts('media', CookieConsent.acceptedCategory('media'));
}

export async function initializeConsent(
    config: ConsentConfig,
    localeCode?: string,
): Promise<void> {
    const locale = resolveConsentLocale(localeCode);

    if (initialized) {
        await CookieConsent.setLanguage(locale, true);
        return;
    }

    registerDefaults(config);

    const iframeManager = window.iframemanager?.();

    if (iframeManager) {
        iframeManager.run({
            currLang: locale,
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
            default: locale,
            translations: {
                en: consentTranslations.en,
                fr: consentTranslations.fr,
            },
        },
        onFirstConsent: syncConsentState,
        onConsent: syncConsentState,
        onChange: syncConsentState,
    });

    window.SidewalkConsent = {
        showPreferences: () => CookieConsent.showPreferences(),
        acceptedCategory: (category: string) =>
            CookieConsent.acceptedCategory(category),
    };

    initialized = true;
}
