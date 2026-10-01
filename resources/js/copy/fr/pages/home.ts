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
    plan: {
        caption: 'Les pages du site, les projets et ce que chacun contient.',
        edges: {
            design: 'design',
            engine: 'moteur',
        },
        legend: [
            'Rouge : vous êtes ici',
            'Encre : les pages du site',
            'Bleu : astralmanach et ses parties',
            'Trait entre deux lots : un lien ou une interface',
        ],
        lots: {
            api: { label: 'API publique', note: 'OpenAPI, Postman, 34 sources' },
            app: {
                label: 'DD le Dépanneur\nAtlas Dépannage',
                note: 'Une app, deux marques',
            },
            aremedia: { label: 'Aremedia', note: 'Santé publique et éducation populaire, Paris : un outil de terrain auto-hébergé et le site public' },
            astralmanach: {
                label: 'astralmanach',
                note: 'Le ciel d’une date, en données',
            },
            caseConsent: {
                label: 'Consentement et mesure',
                note: 'Étude de cas : le consentement avant la mesure',
            },
            caseDeploy: {
                label: 'Déploiement e-commerce',
                note: 'Étude de cas : un pipeline de déploiement',
            },
            caseFlux: {
                label: 'Flux ERP et PIM',
                note: 'Étude de cas : les données produit entre ERP, PIM et boutique',
            },
            cases: { label: 'Études de cas', note: 'Quatre cas rédigés' },
            caseTools: {
                label: 'Outil associatif',
                note: 'Étude de cas : auto-hébergement et données sensibles',
            },
            clientAuberi: { label: 'Auberi', note: 'Maison cliente de Jewely, site e-commerce' },
            clientCrown: { label: 'Crown-DP', note: 'Maison cliente de Jewely, site e-commerce' },
            clientGodechot: { label: 'Godechot-Pauliet', note: 'Maison cliente de Jewely, site e-commerce' },
            clientJulian: { label: 'Louis Julian', note: 'Maison cliente de Jewely, site e-commerce' },
            contact: { label: 'Contact', note: 'Nancy, Paris, à distance' },
            experience: { label: 'Expérience', note: 'Jewely, Aremedia, Parcours le Monde' },
            florian: {
                label: 'florianrosinski.fr',
                note: "Portfolio de Florian Rosinski, que j'ai développé",
            },
            home: { label: 'Accueil', note: 'Vous êtes ici' },
            jewely: { label: 'Jewely / Flippad', note: 'ERP, PIM et e-commerce pour l’horlogerie-bijouterie' },
            journal: { label: 'Journal', note: 'Notes de travail' },
            labs: { label: 'Labs', note: 'Audit Core Web Vitals et bacs à sable' },
            lib: { label: 'Bibliothèque npm', note: 'Licence MIT, ESM et types' },
            obs: {
                label: 'Observations',
                note: 'Une planche imprimable du ciel d’une date',
            },
            plm: { label: 'Parcours le Monde', note: 'Mobilité internationale des jeunes, toujours active à Marseille' },
            rolexBespoke: { label: 'Rolex Bespoke', note: 'Dispositif Rolex : toutes les demandes passent par moi' },
            rolexCpo: { label: 'Rolex Certified Pre-Owned', note: 'Dispositif Rolex : toutes les demandes passent par moi' },
            services: { label: 'Services', note: 'Stacks et tarifs de départ' },
            tools: {
                label: 'Thème, Ciel,\nCertificat',
                note: 'Trois outils publics',
            },
            uavv: {
                label: 'Un art voulu voyant',
                note: "Site d'un projet artistique de Florian Rosinski, que j'ai développé",
            },
            work: { label: 'Réalisations', note: 'Quatre lots, un par projet' },
        },
        revision: 'Ind. B · 2026-10-01',
        scale: '1 bloc = 1 lot',
        title: 'Plan du travail',
    },
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
