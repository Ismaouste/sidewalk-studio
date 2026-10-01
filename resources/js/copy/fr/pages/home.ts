/**
 * French copy. English is the reference shape: `satisfies` reports a key that
 * is missing here and a key that exists only here, so the two locales cannot
 * drift apart silently. Keys are sorted; `sort-keys` enforces it in lint.
 */
type Reference = typeof import('../../en/pages/home').default;

export default {
    apiDefinition:
        "API : interface d'échange entre services, outils métier et applications.",
    archiveCta: 'Découvrir toutes les études de cas',
    ciCdDefinition:
        'CI/CD : intégration et déploiement continus pour fiabiliser les mises en ligne.',
    clients: {
        figures: [
            { label: 'ans chez Jewely / Flippad', suffix: '', value: 5 },
            { label: 'fiches produit enrichies', suffix: '+', value: 20000 },
            { label: 'maisons clientes', suffix: '', value: 4 },
        ],
        houses: [
            {
                href: 'https://www.crown-dp.com/fr',
                linkLabel: 'crown-dp.com',
                name: 'Crown-DP',
                period: '2025',
                role: 'Montres de luxe à Strasbourg. Réalisé seul, de A à Z : j’ai intégré la maquette fournie par une agence de Strasbourg, et tout le monde a été ravi du résultat.',
            },
            {
                href: 'https://www.godechot-pauliet.com/en/rolex-certified-pre-owned/',
                linkLabel: 'Espace Rolex Certified Pre-Owned',
                name: 'Godechot-Pauliet',
                period: '',
                role: 'J’ai réalisé son espace Rolex Certified Pre-Owned.',
            },
            {
                href: '',
                linkLabel: '',
                name: 'Louis Julian',
                period: '',
                role: 'J’ai réalisé son espace Rolex.',
            },
            {
                href: '',
                linkLabel: '',
                name: 'Auberi',
                period: '',
                role: 'Site e-commerce sur la plateforme commune.',
            },
        ],
        intro: 'Quatre maisons du portefeuille clients de Jewely, sur un même back end commun. Sous NDA avec Rolex ; les demandes Rolex Bespoke passaient aussi par moi.',
        title: 'Réalisé pour les maisons clientes de Jewely',
    },
    cmsDefinition: 'Content Management System : système de gestion de contenu.',
    contactCta: 'Prendre contact',
    contactLabel: 'Contact',
    currentFrameLabel: 'Dernier poste',
    dataLayerDefinition:
        'Data layer : couche de données partagée entre le site, le tracking et les outils marketing.',
    focusDescription:
        'Livraison produit, modernisation de systèmes existants, SEO technique et vie privée, et entretien des systèmes après la mise en production.',
    focusTitle: 'Sur quoi je travaille',
    hbjoatDefinition: 'Horlogerie, bijouterie, joaillerie et orfèvrerie.',
    hbjoatLabel: 'HBJO',
    heroCapabilities: [
        {
            details:
                'WooCommerce / PrestaShop / Shopify / Alokai (ex Vue Storefront)',
            label: 'Sites marchands',
            panelDetails: 'WooCommerce / PrestaShop / Shopify / Alokai',
            tone: 'violet' as const,
        },
        {
            details: 'Laravel / PHP / APIs / CI-CD',
            label: 'Laravel',
            panelDetails: 'Laravel / PHP / APIs / CI-CD',
            tone: 'green' as const,
        },
        {
            details: 'PIM / JSON-LD / Merchant Center / Data layer',
            label: 'Data produit et SEO',
            panelDetails: 'PIM / JSON-LD / Merchant Center / Data layer',
            tone: 'sun' as const,
        },
    ],
    heroPanelSummarySuffix:
        'écosystème HBJO, ERP, PIM, flux produit, tracking et SEO technique.',
    heroPanelTitle: 'Développeur e-commerce, Jewely / Flippad (2021–2026)',
    internalBuildLabel: 'Interne',
    jsonLdDefinition:
        'JSON-LD : format de données structurées lisible par les moteurs et les plateformes.',
    laravelDefinition: 'Framework PHP pour applications web modernes.',
    merchantCenterDefinition:
        'Google Merchant Center : flux catalogue et diffusion produit vers les surfaces shopping Google.',
    notesLabel: 'Notes',
    openProjectsCta: 'Découvrir les projets',
    phpDefinition:
        'PHP : langage serveur largement utilisé pour les applications web et e-commerce.',
    pimDefinition:
        'PIM : Product Information Management, le socle qui centralise et structure la donnée produit.',
    projectsCta: 'Lire les expériences',
    projectsDescription: 'Cas rédigés et notes sur des situations concrètes.',
    projectsTitle: 'Études de cas et notes',
    referencesCta: 'Lire les expériences',
    selectedWorkLabel: 'Expérience',
    seoDefinition:
        'SEO : optimisation technique et éditoriale pour rendre un site lisible par les moteurs et utile aux visiteurs.',
    startConversationCta: 'Prendre contact',
    whatIDoLabel: 'Ce que je fais',
} satisfies Reference;
