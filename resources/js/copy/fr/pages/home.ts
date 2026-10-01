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
            api: {
                label: 'API publique',
                note: 'OpenAPI, Postman, 34 sources',
            },
            app: {
                label: 'DD le Dépanneur\nAtlas Dépannage',
                note: 'Une app, deux marques',
            },
            aremedia: {
                label: 'Aremedia',
                note: 'Santé publique et éducation populaire, Paris : un outil de terrain auto-hébergé et le site public',
            },
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
            clientAuberi: {
                label: 'Auberi',
                note: 'Maison cliente de Jewely, site e-commerce',
            },
            clientCrown: {
                detail: {
                    links: [
                        {
                            href: 'https://www.crown-dp.com/fr',
                            label: 'crown-dp.com',
                            nofollow: true,
                        },
                    ],
                    period: '2025',
                    role: 'Réalisé seul, de A à Z. J’ai intégré la maquette fournie par une agence de Strasbourg.',
                },
                label: 'Crown-DP',
                note: 'Montres de luxe à Strasbourg : site e-commerce sur mesure',
            },
            clientGodechot: {
                detail: {
                    links: [
                        {
                            href: 'https://www.godechot-pauliet.com/en/rolex-certified-pre-owned/',
                            label: 'Espace Rolex Certified Pre-Owned',
                            nofollow: true,
                        },
                    ],
                    role: 'Maison cliente de Jewely. J’ai réalisé son espace Rolex Certified Pre-Owned.',
                },
                label: 'Godechot-Pauliet',
                note: 'Maison cliente de Jewely, site e-commerce',
            },
            clientJulian: {
                detail: {
                    role: 'Maison cliente de Jewely. J’ai réalisé son espace Rolex.',
                },
                label: 'Louis Julian',
                note: 'Maison cliente de Jewely, site e-commerce',
            },
            contact: { label: 'Contact', note: 'Nancy, Paris, à distance' },
            experience: {
                label: 'Expérience',
                note: 'Jewely, Aremedia, Parcours le Monde',
            },
            florian: {
                label: 'florianrosinski.fr',
                note: "Portfolio de Florian Rosinski, que j'ai développé",
            },
            home: { label: 'Accueil', note: 'Vous êtes ici' },
            jewely: {
                detail: {
                    figures: [
                        { label: 'ans, de 2021 à 2026', value: '5' },
                        { label: 'fiches produit enrichies', value: '20 000+' },
                    ],
                    links: [
                        { href: '/experience', label: 'Lire l’expérience' },
                    ],
                    role: 'Développeur e-commerce. 2026 : un CMS sur mesure à back end commun pour plusieurs maisons clientes.',
                },
                label: 'Jewely / Flippad',
                note: 'ERP, PIM et e-commerce pour l’horlogerie-bijouterie',
            },
            journal: { label: 'Journal', note: 'Notes de travail' },
            labs: {
                label: 'Labs',
                note: 'Audit Core Web Vitals et bacs à sable',
            },
            lib: {
                label: 'Bibliothèque npm',
                note: 'Licence MIT, ESM et types',
            },
            obs: {
                label: 'Observations',
                note: 'Une planche imprimable du ciel d’une date',
            },
            plm: {
                label: 'Parcours le Monde',
                note: 'Mobilité internationale des jeunes, toujours active à Marseille',
            },
            rolexBespoke: {
                detail: {
                    role: 'Toutes les demandes passaient par moi. Sous NDA avec Rolex.',
                },
                label: 'Rolex Bespoke',
                note: 'Dispositif Rolex : toutes les demandes passent par moi',
            },
            rolexCpo: {
                label: 'Rolex Certified\nPre-Owned',
                note: 'Espace Rolex de Godechot-Pauliet',
            },
            rolexJulian: {
                label: 'Espace Rolex',
                note: 'Espace Rolex de Louis Julian',
            },
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
