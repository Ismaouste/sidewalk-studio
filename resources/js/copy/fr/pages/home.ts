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
    cmsDefinition: 'Content Management System : système de gestion de contenu.',
    contactCta: 'Prendre contact',
    contactLabel: 'Contact',
    currentFrameLabel: "Aujourd'hui",
    dataLayerDefinition:
        'Data layer : couche de données partagée entre le site, le tracking et les outils marketing.',
    focusDescription:
        'Le travail se situe souvent entre livraison produit, modernisation du legacy, SEO technique, vie privée et besoin de garder des systèmes compréhensibles après mise en production.',
    focusTitle: 'Un positionnement net dans des environnements complexes.',
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
    heroPanelTitle: 'Développeur e-commerce chez Jewely / Flippad',
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
    projectsDescription:
        'Études de cas, notes et références pour entrer dans des situations plus concrètes.',
    projectsTitle: 'Études de cas et repères à ouvrir ensuite.',
    referencesCta: 'Lire les expériences',
    selectedWorkLabel: 'Expérience',
    seoDefinition:
        'SEO : optimisation technique et éditoriale pour rendre un site lisible par les moteurs et utile aux visiteurs.',
    startConversationCta: 'Prendre contact',
    whatIDoLabel: 'Ce que je fais',
} satisfies Reference;
